<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Component;

class AdminDashboard extends Component
{
    public function render(): View
    {
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $startOfLastMonth = now()->subMonth()->startOfMonth()->toDateString();
        $endOfLastMonth = now()->subMonth()->endOfMonth()->toDateString();

        $pendapatanBulanIni = Invoice::where('payment_status', 'paid')
            ->whereBetween('tanggal', [$startOfMonth, $today])
            ->sum('grand_total');

        $pendapatanBulanLalu = Invoice::where('payment_status', 'paid')
            ->whereBetween('tanggal', [$startOfLastMonth, $endOfLastMonth])
            ->sum('grand_total');

        $deltaPercent = $pendapatanBulanLalu > 0
            ? round((($pendapatanBulanIni - $pendapatanBulanLalu) / $pendapatanBulanLalu) * 100, 1)
            : null;

        $bookingPending = Booking::where('status', 'pending')->count();
        $invoicePending = Invoice::where('payment_status', 'unpaid')->count();
        $pelangganBaru = User::where('role', 'customer')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $stokKritis = Sparepart::whereColumn('stock', '<=', 'minimum_stock')
            ->where('is_active', true)
            ->count();

        $bookingsPending = Booking::with(['vehicle'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $invoicesBelumLunas = Invoice::with(['customer', 'partner'])
            ->where('payment_status', '!=', 'paid')
            ->latest('tanggal')
            ->take(5)
            ->get();

        $sparepartKritis = Sparepart::whereColumn('stock', '<=', 'minimum_stock')
            ->where('is_active', true)
            ->orderBy('stock')
            ->take(10)
            ->get();

        return view('livewire.admin.dashboard.index', [
            'pendapatanBulanIni' => $pendapatanBulanIni,
            'deltaPercent' => $deltaPercent,
            'bookingPending' => $bookingPending,
            'invoicePending' => $invoicePending,
            'pelangganBaru' => $pelangganBaru,
            'stokKritis' => $stokKritis,
            'bookingsPending' => $bookingsPending,
            'invoicesBelumLunas' => $invoicesBelumLunas,
            'sparepartKritis' => $sparepartKritis,
        ])->layout('layouts.admin', ['title' => 'Dashboard']);
    }
}
