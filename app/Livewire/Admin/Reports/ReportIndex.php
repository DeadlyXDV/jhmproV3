<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;

class ReportIndex extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.reports.index')
            ->layout('layouts.admin');
    }
}
