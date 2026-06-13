<?php

namespace App\Livewire\Mekanik\Dashboard;

use App\Models\WorkOrder;
use Illuminate\View\View;
use Livewire\Component;

class MekanikDashboard extends Component
{
    public function render(): View
    {
        $mekanikId = auth('admin')->id();

        $antrian = WorkOrder::where('mekanik_id', $mekanikId)
            ->where('status', 'antrian')
            ->with(['vehicle', 'vehicle.customer'])
            ->latest()
            ->get();

        $sedangProses = WorkOrder::where('mekanik_id', $mekanikId)
            ->where('status', 'proses')
            ->with(['vehicle', 'vehicle.customer'])
            ->latest()
            ->get();

        $selesaiHariIni = WorkOrder::where('mekanik_id', $mekanikId)
            ->where('status', 'selesai')
            ->whereDate('selesai_at', today())
            ->count();

        return view('livewire.mekanik.dashboard.index', compact('antrian', 'sedangProses', 'selesaiHariIni'))
            ->layout('layouts.mekanik', ['title' => 'Dashboard Mekanik']);
    }
}
