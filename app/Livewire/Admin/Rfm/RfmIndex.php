<?php

namespace App\Livewire\Admin\Rfm;

use App\Models\ClusterDefinition;
use App\Models\CustomerRfm;
use App\Models\RfmHistory;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RfmIndex extends Component
{
    use WithPagination;

    public string $activeSource = 'bengkel';

    public string $filterCluster = '';

    public string $search = '';

    public function updatedActiveSource(): void
    {
        $this->resetPage();
        $this->filterCluster = '';
    }

    public function updatedFilterCluster(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function exportCsv(): StreamedResponse
    {
        $query = CustomerRfm::query()
            ->with('customer')
            ->where('source', $this->activeSource)
            ->when($this->filterCluster, fn ($q) => $q->where('cluster_label', $this->filterCluster))
            ->when($this->search, fn ($q) => $q->whereHas('customer', fn ($r) => $r->where('nama', 'like', "%{$this->search}%")))
            ->orderByDesc('rfm_score')
            ->get();

        $filename = 'rfm_'.$this->activeSource.($this->filterCluster ? '_'.$this->filterCluster : '').'_'.now()->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Nama', 'No HP', 'Email', 'Segmen', 'R Score', 'F Score', 'M Score', 'RFM Score', 'Recency (hari)', 'Frequency', 'Monetary', 'Dihitung Pada']);
            foreach ($query as $row) {
                fputcsv($handle, [
                    $row->customer?->nama ?? '-',
                    $row->customer?->no_hp ?? '-',
                    $row->customer?->email ?? '-',
                    $row->cluster_label,
                    $row->r_score,
                    $row->f_score,
                    $row->m_score,
                    $row->rfm_score,
                    $row->recency_days,
                    $row->frequency,
                    $row->monetary,
                    $row->calculated_at,
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function render(): View
    {
        $clusters = ClusterDefinition::orderBy('id')->get();

        $baseQuery = CustomerRfm::query()
            ->with('customer')
            ->where('source', $this->activeSource);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'avg_monetary' => (clone $baseQuery)->avg('monetary') ?? 0,
        ];

        $clusterStats = (clone $baseQuery)
            ->selectRaw('cluster_label, count(*) as total')
            ->groupBy('cluster_label')
            ->pluck('total', 'cluster_label');

        $latestCalculatedAt = (clone $baseQuery)->max('calculated_at');

        $clusterAnalytics = CustomerRfm::query()
            ->where('source', $this->activeSource)
            ->selectRaw('cluster_id, cluster_label,
                count(*) as total,
                round(avg(r_score), 2) as avg_r,
                round(avg(f_score), 2) as avg_f,
                round(avg(m_score), 2) as avg_m,
                round(avg(recency_days), 1) as avg_recency,
                round(avg(frequency), 1) as avg_freq,
                round(avg(monetary), 2) as avg_monetary')
            ->groupBy('cluster_id', 'cluster_label')
            ->get()
            ->keyBy('cluster_label');

        $trendMonths = RfmHistory::where('source', $this->activeSource)
            ->selectRaw('`year_month`, `cluster_label`, count(*) as total')
            ->groupBy('year_month', 'cluster_label')
            ->orderBy('year_month')
            ->get()
            ->groupBy('year_month');

        $rows = (clone $baseQuery)
            ->when($this->filterCluster, fn ($q) => $q->where('cluster_label', $this->filterCluster))
            ->when($this->search, function ($q) {
                $q->whereHas('customer', fn ($r) => $r->where('nama', 'like', "%{$this->search}%"));
            })
            ->orderByDesc('rfm_score')
            ->paginate(20);

        return view('livewire.admin.rfm.index', compact(
            'clusters', 'stats', 'clusterStats', 'latestCalculatedAt', 'rows',
            'clusterAnalytics', 'trendMonths'
        ))->layout('layouts.admin', ['title' => 'Segmentasi Pelanggan']);
    }
}
