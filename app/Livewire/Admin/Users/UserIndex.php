<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterRole = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterRole(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->id === auth('admin')->id()) {
            return;
        }

        $user->update(['is_active' => ! $user->is_active]);
    }

    public function render(): View
    {
        $users = User::query()
            ->whereIn('role', ['super_admin', 'admin', 'mekanik'])
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterRole, fn ($q) => $q->where('role', $this->filterRole))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.users.index', compact('users'))
            ->layout('layouts.admin', ['title' => 'Pengguna']);
    }
}
