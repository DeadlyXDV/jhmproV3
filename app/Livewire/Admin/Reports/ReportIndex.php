<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;

class ReportIndex extends Component
{
    public string $dateFrom = '';

    public string $dateTo = '';

    public string $filterTipe = '';

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function render(): View
    {
        $from = Carbon::parse($this->dateFrom)->startOfDay();
        $to = Carbon::parse($this->dateTo)->endOfDay();

        $invoiceQuery = Invoice::query()
            ->whereBetween('tanggal', [$from, $to]);

        if ($this->filterTipe === 'bengkel') {
            $invoiceQuery->whereIn('tipe', ['jasa', 'sparepart', 'bundle', 'campuran', 'walk_in']);
        }

        $totalPendapatanInvoice = (clone $invoiceQuery)->sum('grand_total');
        $jumlahInvoice = (clone $invoiceQuery)->count();

        $totalPendapatanOrder = 0;
        $jumlahOrder = 0;
        if ($this->filterTipe !== 'bengkel') {
            $orderQuery = Order::query()
                ->where('payment_status', 'paid')
                ->whereBetween('created_at', [$from, $to]);
            $totalPendapatanOrder = $orderQuery->sum('grand_total');
            $jumlahOrder = (clone $orderQuery)->count();
        }

        $totalPendapatan = $this->filterTipe === 'online'
            ? $totalPendapatanOrder
            : ($this->filterTipe === 'bengkel' ? $totalPendapatanInvoice : $totalPendapatanInvoice + $totalPendapatanOrder);

        $jumlahTransaksi = $this->filterTipe === 'online'
            ? $jumlahOrder
            : ($this->filterTipe === 'bengkel' ? $jumlahInvoice : $jumlahInvoice + $jumlahOrder);

        $totalHpp = InvoiceItem::query()
            ->whereHas('invoice', fn ($q) => $q->whereBetween('tanggal', [$from, $to]))
            ->whereNotNull('sparepart_id')
            ->sum(DB::raw('qty * harga_beli_snapshot'));

        $grossMargin = $totalPendapatan > 0
            ? round(($totalPendapatan - $totalHpp) / $totalPendapatan * 100, 1)
            : 0;

        $driver = DB::getDriverName();
        $dateTrunc = $driver === 'pgsql'
            ? "TO_CHAR(tanggal, 'YYYY-MM') as bulan"
            : "DATE_FORMAT(tanggal, '%Y-%m') as bulan";
        $groupBy = $driver === 'pgsql'
            ? "TO_CHAR(tanggal, 'YYYY-MM')"
            : "DATE_FORMAT(tanggal, '%Y-%m')";

        $trenBulanan = Invoice::query()
            ->selectRaw("{$dateTrunc}, SUM(grand_total) as total")
            ->whereBetween('tanggal', [now()->subMonths(5)->startOfMonth(), $to])
            ->groupByRaw($groupBy)
            ->orderBy('bulan')
            ->get();

        $topServices = InvoiceItem::query()
            ->selectRaw('service_id, SUM(subtotal) as total_revenue, SUM(qty) as total_qty')
            ->with('service')
            ->whereNotNull('service_id')
            ->whereHas('invoice', fn ($q) => $q->whereBetween('tanggal', [$from, $to]))
            ->groupBy('service_id')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        $topSpareparts = InvoiceItem::query()
            ->selectRaw('sparepart_id, SUM(qty) as total_qty, SUM(subtotal) as total_revenue')
            ->with('sparepart')
            ->whereNotNull('sparepart_id')
            ->whereHas('invoice', fn ($q) => $q->whereBetween('tanggal', [$from, $to]))
            ->groupBy('sparepart_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('livewire.admin.reports.index', compact(
            'totalPendapatan', 'jumlahTransaksi', 'totalHpp', 'grossMargin',
            'trenBulanan', 'topServices', 'topSpareparts'
        ))->layout('layouts.admin', ['title' => 'Laporan Keuangan']);
    }
}
