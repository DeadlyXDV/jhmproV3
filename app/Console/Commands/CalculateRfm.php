<?php

namespace App\Console\Commands;

use App\Models\ClusterDefinition;
use App\Models\CustomerRfm;
use App\Models\RfmHistory;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CalculateRfm extends Command
{
    protected $signature = 'rfm:calculate {--source=all : bengkel|combined|all}';

    protected $description = 'Hitung RFM score dan jalankan K-Means clustering per customer';

    public function handle(): int
    {
        $k = (int) Setting::get('rfm_k_clusters', 5);
        $weightR = (float) Setting::get('rfm_weight_r', 0.3);
        $weightF = (float) Setting::get('rfm_weight_f', 0.3);
        $weightM = (float) Setting::get('rfm_weight_m', 0.4);
        $periodMonths = (int) Setting::get('rfm_period_months', 12);

        $periodStart = now()->subMonths($periodMonths)->toDateString();
        $periodEnd = now()->toDateString();
        $today = now();

        $sourceFn = $this->option('source');
        $sources = $sourceFn === 'all' ? ['bengkel', 'combined'] : [$sourceFn];

        $clusterDefs = ClusterDefinition::orderBy('id')->pluck('label', 'id')->toArray();
        $clusterIds = array_keys($clusterDefs);

        $this->info("Mulai kalkulasi RFM — period: {$periodStart} s/d {$periodEnd}");

        foreach ($sources as $source) {
            $rows = $this->fetchRawRfm($source, $periodStart);

            if ($rows->isEmpty()) {
                $this->line("  [{$source}] Tidak ada data — skip.");

                continue;
            }

            $customerIds = $rows->pluck('customer_id')->all();
            $recencyDays = $rows->pluck('last_txn', 'customer_id')
                ->map(fn ($d) => (int) abs($today->diffInDays(Carbon::parse($d))))
                ->all();
            $frequencies = $rows->pluck('freq', 'customer_id')->map(fn ($v) => (int) $v)->all();
            $monetaries = $rows->pluck('monetary', 'customer_id')->map(fn ($v) => (float) $v)->all();

            $rScores = $this->quintilesInverse($recencyDays);
            $fScores = $this->quintiles($frequencies);
            $mScores = $this->quintiles($monetaries);

            $points = [];
            foreach ($customerIds as $cid) {
                $points[$cid] = [(float) $rScores[$cid], (float) $fScores[$cid], (float) $mScores[$cid]];
            }

            $effectiveK = min($k, count($points));
            $result = $this->kMeans($points, $effectiveK, $clusterIds);

            $yearMonth = now()->format('Y-m');
            $calculatedAt = now()->toDateTimeString();

            foreach ($customerIds as $cid) {
                $clusterIdx = $result['assignments'][$cid];
                $clusterLabel = $clusterDefs[$clusterIdx] ?? 'Unknown';
                $rfmScore = round(
                    $rScores[$cid] * $weightR + $fScores[$cid] * $weightF + $mScores[$cid] * $weightM,
                    2
                );

                CustomerRfm::updateOrCreate(
                    ['customer_id' => $cid, 'source' => $source],
                    [
                        'recency_days' => $recencyDays[$cid],
                        'frequency' => $frequencies[$cid],
                        'monetary' => $monetaries[$cid],
                        'r_score' => $rScores[$cid],
                        'f_score' => $fScores[$cid],
                        'm_score' => $mScores[$cid],
                        'rfm_score' => $rfmScore,
                        'cluster_id' => $clusterIdx,
                        'cluster_label' => $clusterLabel,
                        'period_start' => $periodStart,
                        'period_end' => $periodEnd,
                        'calculated_at' => $calculatedAt,
                    ]
                );

                $historyExists = RfmHistory::where('customer_id', $cid)
                    ->where('source', $source)
                    ->where('year_month', $yearMonth)
                    ->exists();

                if (! $historyExists) {
                    RfmHistory::create([
                        'customer_id' => $cid,
                        'source' => $source,
                        'year_month' => $yearMonth,
                        'recency_days' => $recencyDays[$cid],
                        'frequency' => $frequencies[$cid],
                        'monetary' => $monetaries[$cid],
                        'cluster_id' => $clusterIdx,
                        'cluster_label' => $clusterLabel,
                        'created_at' => $calculatedAt,
                    ]);
                }
            }

            foreach ($result['centroids'] as $clusterIdx => $centroid) {
                $def = ClusterDefinition::find($clusterIdx);
                if ($def) {
                    $def->update(['centroid' => $centroid]);
                }
            }

            $this->info("  [{$source}] {$rows->count()} customer diproses — {$effectiveK} cluster.");
        }

        $this->info('Kalkulasi RFM selesai.');

        return self::SUCCESS;
    }

    private function fetchRawRfm(string $source, string $periodStart): Collection
    {
        if ($source === 'bengkel') {
            return DB::table('invoices')
                ->where('payment_status', 'paid')
                ->whereIn('tipe', ['walk_in', 'booking', 'partner'])
                ->where('tanggal', '>=', $periodStart)
                ->whereNotNull('customer_id')
                ->groupBy('customer_id')
                ->select([
                    'customer_id',
                    DB::raw('MAX(tanggal) as last_txn'),
                    DB::raw('COUNT(*) as freq'),
                    DB::raw('SUM(grand_total) as monetary'),
                ])
                ->get();
        }

        // combined: bengkel + online_shop merged in PHP
        $bengkelRows = DB::table('invoices')
            ->where('payment_status', 'paid')
            ->whereIn('tipe', ['walk_in', 'booking', 'partner'])
            ->where('tanggal', '>=', $periodStart)
            ->whereNotNull('customer_id')
            ->select(['customer_id', DB::raw('tanggal as txn_date'), DB::raw('grand_total as amount')])
            ->get();

        $shopRows = DB::table('orders')
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $periodStart)
            ->whereNotNull('customer_id')
            ->select(['customer_id', DB::raw('DATE(created_at) as txn_date'), DB::raw('grand_total as amount')])
            ->get();

        $all = $bengkelRows->concat($shopRows);

        if ($all->isEmpty()) {
            return collect();
        }

        return $all->groupBy('customer_id')->map(function ($group, $customerId) {
            return (object) [
                'customer_id' => $customerId,
                'last_txn' => $group->max('txn_date'),
                'freq' => $group->count(),
                'monetary' => $group->sum('amount'),
            ];
        })->values();
    }

    /**
     * Quintile scoring: higher value → higher score (1–5).
     *
     * @param  array<int|string, int|float>  $values
     * @return array<int|string, int>
     */
    private function quintiles(array $values): array
    {
        $n = count($values);
        if ($n === 0) {
            return [];
        }

        asort($values);
        $sorted = array_keys($values);
        $scores = [];

        foreach ($sorted as $rank => $id) {
            $scores[$id] = (int) min(5, floor($rank * 5 / $n) + 1);
        }

        return $scores;
    }

    /**
     * Quintile scoring inverted: lower value → higher score (1–5). Used for Recency.
     *
     * @param  array<int|string, int|float>  $values
     * @return array<int|string, int>
     */
    private function quintilesInverse(array $values): array
    {
        $inverted = array_map(fn ($v) => -$v, $values);

        return $this->quintiles($inverted);
    }

    /**
     * K-Means clustering.
     *
     * @param  array<int|string, array<int, float>>  $points  keyed by customer_id
     * @param  array<int, int>  $clusterIds  ClusterDefinition IDs ordered by prestige (Champion first)
     * @return array{assignments: array<int|string, int>, centroids: array<int, array<int, float>>}
     */
    private function kMeans(array $points, int $k, array $clusterIds): array
    {
        $customerIds = array_keys($points);
        $n = count($customerIds);

        if ($n <= $k) {
            $assignments = [];
            foreach ($customerIds as $i => $cid) {
                $assignments[$cid] = $clusterIds[$i] ?? $clusterIds[0];
            }
            $centroids = [];
            foreach ($assignments as $cid => $clIdx) {
                $centroids[$clIdx] = $points[$cid];
            }

            return ['assignments' => $assignments, 'centroids' => $centroids];
        }

        // Initialize centroids: pick first k shuffled points
        $shuffled = $customerIds;
        shuffle($shuffled);
        $internalCentroids = [];
        for ($i = 0; $i < $k; $i++) {
            $internalCentroids[$i] = array_values($points[$shuffled[$i]]);
        }

        $assignments = [];
        $maxIter = 100;

        for ($iter = 0; $iter < $maxIter; $iter++) {
            $newAssignments = [];

            foreach ($customerIds as $cid) {
                $bestCluster = 0;
                $bestDist = PHP_FLOAT_MAX;
                for ($c = 0; $c < $k; $c++) {
                    $dist = $this->euclidean($points[$cid], $internalCentroids[$c]);
                    if ($dist < $bestDist) {
                        $bestDist = $dist;
                        $bestCluster = $c;
                    }
                }
                $newAssignments[$cid] = $bestCluster;
            }

            if ($newAssignments === $assignments) {
                break;
            }
            $assignments = $newAssignments;

            $dims = count(reset($points));
            for ($c = 0; $c < $k; $c++) {
                $members = array_filter($customerIds, fn ($cid) => $assignments[$cid] === $c);
                if (! empty($members)) {
                    $centroid = array_fill(0, $dims, 0.0);
                    foreach ($members as $cid) {
                        foreach ($points[$cid] as $d => $val) {
                            $centroid[$d] += $val;
                        }
                    }
                    $cnt = count($members);
                    $internalCentroids[$c] = array_map(fn ($v) => $v / $cnt, $centroid);
                }
            }
        }

        // Map internal indices (0..k-1) → ClusterDefinition IDs
        // Highest centroid sum → Champion (first in $clusterIds)
        $centroidSums = [];
        for ($c = 0; $c < $k; $c++) {
            $centroidSums[$c] = array_sum($internalCentroids[$c]);
        }
        arsort($centroidSums);

        $internalToCluster = [];
        $i = 0;
        foreach (array_keys($centroidSums) as $internalIdx) {
            $internalToCluster[$internalIdx] = $clusterIds[$i] ?? end($clusterIds);
            $i++;
        }

        $finalAssignments = [];
        foreach ($assignments as $cid => $internalIdx) {
            $finalAssignments[$cid] = $internalToCluster[$internalIdx];
        }

        $finalCentroids = [];
        foreach ($internalToCluster as $internalIdx => $clusterDefId) {
            $finalCentroids[$clusterDefId] = array_map(
                fn ($v) => round((float) $v, 4),
                $internalCentroids[$internalIdx]
            );
        }

        return ['assignments' => $finalAssignments, 'centroids' => $finalCentroids];
    }

    /** @param array<int, float|int> $b */
    private function euclidean(array $a, array $b): float
    {
        $sum = 0.0;
        foreach ($a as $i => $val) {
            $diff = $val - ($b[$i] ?? 0.0);
            $sum += $diff * $diff;
        }

        return sqrt($sum);
    }
}
