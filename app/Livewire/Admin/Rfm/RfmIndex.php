<?php

namespace App\Livewire\Admin\Rfm;

use App\Models\ClusterDefinition;
use App\Models\CustomerRfm;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

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

        $rows = (clone $baseQuery)
            ->when($this->filterCluster, fn ($q) => $q->where('cluster_label', $this->filterCluster))
            ->when($this->search, function ($q) {
                $q->whereHas('customer', fn ($r) => $r->where('nama', 'like', "%{$this->search}%"));
            })
            ->orderByDesc('rfm_score')
            ->paginate(20);

        return view('livewire.admin.rfm.index', compact(
            'clusters', 'stats', 'clusterStats', 'latestCalculatedAt', 'rows'
        ))->layout('layouts.admin', ['title' => 'Segmentasi RFM']);
    }
}
