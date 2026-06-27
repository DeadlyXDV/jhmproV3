<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\CustomerRfm;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Sparepart;
use App\Models\WorkOrder;
use Illuminate\View\View;
use Livewire\Component;

class AdminDashboard extends Component
{
    public function render(): View
    {
        $today = now()->toDateString();
        $currentYear = now()->year;
        $startOfMonth = now()->startOfMonth()->toDateString();
        $startOfLastMonth = now()->subMonthNoOverflow()->startOfMonth()->toDateString();
        $endOfLastMonth = now()->subMonthNoOverflow()->endOfMonth()->toDateString();

        // ── Stat cards ──────────────────────────────────────────────────────
        $pendapatanBulanIni = Invoice::where('payment_status', 'paid')
            ->whereBetween('tanggal', [$startOfMonth, $today])
            ->sum('grand_total');

        $pendapatanBulanLalu = Invoice::where('payment_status', 'paid')
            ->whereBetween('tanggal', [$startOfLastMonth, $endOfLastMonth])
            ->sum('grand_total');

        $deltaPercent = $pendapatanBulanLalu > 0
            ? round((($pendapatanBulanIni - $pendapatanBulanLalu) / $pendapatanBulanLalu) * 100, 1)
            : null;

        $totalBookingBulanIni = Booking::whereMonth('tanggal_booking', now()->month)
            ->whereYear('tanggal_booking', $currentYear)
            ->count();

        $totalBookingBulanLalu = Booking::whereMonth('tanggal_booking', now()->subMonthNoOverflow()->month)
            ->whereYear('tanggal_booking', now()->subMonthNoOverflow()->year)
            ->count();

        $deltaBookingPercent = $totalBookingBulanLalu > 0
            ? round((($totalBookingBulanIni - $totalBookingBulanLalu) / $totalBookingBulanLalu) * 100, 1)
            : null;

        $woSelesaiBulanIni = WorkOrder::where('status', 'selesai')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', $currentYear)
            ->count();

        $woSelesaiBulanLalu = WorkOrder::where('status', 'selesai')
            ->whereMonth('updated_at', now()->subMonthNoOverflow()->month)
            ->whereYear('updated_at', now()->subMonthNoOverflow()->year)
            ->count();

        $deltaWoPercent = $woSelesaiBulanLalu > 0
            ? round((($woSelesaiBulanIni - $woSelesaiBulanLalu) / $woSelesaiBulanLalu) * 100, 1)
            : null;

        $pelangganBaru = Customer::whereMonth('created_at', now()->month)
            ->whereYear('created_at', $currentYear)
            ->count();

        $pelangganBulanLalu = Customer::whereMonth('created_at', now()->subMonthNoOverflow()->month)
            ->whereYear('created_at', now()->subMonthNoOverflow()->year)
            ->count();

        $deltaPelangganPercent = $pelangganBulanLalu > 0
            ? round((($pelangganBaru - $pelangganBulanLalu) / $pelangganBulanLalu) * 100, 1)
            : null;

        $stokKritis = Sparepart::whereColumn('stock', '<=', 'minimum_stock')
            ->where('is_active', true)
            ->count();

        // ── Work Order donut chart ───────────────────────────────────────────
        $woStats = WorkOrder::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $woTotal = array_sum($woStats);
        $woSelesai = $woStats['selesai'] ?? 0;
        $woProses = $woStats['proses'] ?? 0;
        $woAntrian = $woStats['antrian'] ?? 0;

        // ── Revenue chart — monthly (Jan–Dec current year) ───────────────────
        $servisPerBulan = Invoice::where('payment_status', 'paid')
            ->whereYear('tanggal', $currentYear)
            ->selectRaw('MONTH(tanggal) as bulan, SUM(grand_total) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $shopPerBulan = Order::whereIn('payment_status', ['paid'])
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as bulan, SUM(grand_total) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $revenueData = [];
        for ($m = 1; $m <= 12; $m++) {
            $revenueData[$m] = [
                'servis' => (float) ($servisPerBulan[$m] ?? 0),
                'shop' => (float) ($shopPerBulan[$m] ?? 0),
            ];
        }

        $allValues = array_merge(
            array_column($revenueData, 'servis'),
            array_column($revenueData, 'shop')
        );
        $maxRevenue = max($allValues) ?: 1;

        // ── Booking terbaru ──────────────────────────────────────────────────
        $bookingTerbaru = Booking::with(['customer', 'vehicle', 'mekanik', 'services.service'])
            ->orderByDesc('tanggal_booking')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        // ── Stok kritis ──────────────────────────────────────────────────────
        $sparepartKritis = Sparepart::with('category')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->where('is_active', true)
            ->orderBy('stock')
            ->take(10)
            ->get();

        // ── RFM segmentasi ───────────────────────────────────────────────────
        $rfmSegments = CustomerRfm::where('source', 'combined')
            ->selectRaw('cluster_label, COUNT(*) as total')
            ->groupBy('cluster_label')
            ->pluck('total', 'cluster_label')
            ->toArray();

        $rfmTotal = array_sum($rfmSegments);
        $rfmMax = $rfmTotal > 0 ? max($rfmSegments) : 1;

        return view('livewire.admin.dashboard.index', [
            'pendapatanBulanIni' => $pendapatanBulanIni,
            'deltaPercent' => $deltaPercent,
            'totalBookingBulanIni' => $totalBookingBulanIni,
            'deltaBookingPercent' => $deltaBookingPercent,
            'woSelesaiBulanIni' => $woSelesaiBulanIni,
            'deltaWoPercent' => $deltaWoPercent,
            'pelangganBaru' => $pelangganBaru,
            'deltaPelangganPercent' => $deltaPelangganPercent,
            'stokKritis' => $stokKritis,
            'woTotal' => $woTotal,
            'woSelesai' => $woSelesai,
            'woProses' => $woProses,
            'woAntrian' => $woAntrian,
            'revenueData' => $revenueData,
            'maxRevenue' => $maxRevenue,
            'bookingTerbaru' => $bookingTerbaru,
            'sparepartKritis' => $sparepartKritis,
            'rfmSegments' => $rfmSegments,
            'rfmTotal' => $rfmTotal,
            'rfmMax' => $rfmMax,
        ])->layout('layouts.admin', ['title' => 'Dashboard']);
    }
}
