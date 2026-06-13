<?php

namespace App\Livewire\Admin\Partners;

use App\Models\Partner;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class PartnerIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $partners = Partner::query()
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('nama_bengkel', 'like', "%{$this->search}%")
                        ->orWhere('contact_person', 'like', "%{$this->search}%");
                });
            })
            ->withCount(['invoices as total_kunjungan'])
            ->withSum(['invoices as total_transaksi' => fn ($q) => $q->where('payment_status', 'paid')], 'grand_total')
            ->latest()
            ->paginate(15);

        return view('livewire.admin.partners.index', compact('partners'))
            ->layout('layouts.admin', ['title' => 'Partner']);
    }
}
