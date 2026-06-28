<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterRole = '';

    public bool $showUserForm = false;

    public ?int $editingUserId = null;

    public string $userName = '';

    public string $userEmail = '';

    public string $userRole = 'admin';

    public string $userPassword = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterRole(): void
    {
        $this->resetPage();
    }

    public function openUserCreate(): void
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);
        $this->resetUserForm();
        $this->showUserForm = true;
    }

    public function openUserEdit(int $userId): void
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);
        $user = User::findOrFail($userId);
        abort_if($user->role === 'super_admin', 403);
        $this->editingUserId = $userId;
        $this->userName = $user->name;
        $this->userEmail = $user->email;
        $this->userRole = $user->role;
        $this->userPassword = '';
        $this->showUserForm = true;
    }

    public function saveUser(): void
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);

        $rules = [
            'userName' => ['required', 'string', 'max:255'],
            'userEmail' => ['required', 'email', 'unique:users,email'.($this->editingUserId ? ",{$this->editingUserId}" : '')],
            'userRole' => ['required', 'in:admin,mekanik'],
        ];

        if (! $this->editingUserId) {
            $rules['userPassword'] = ['required', 'string', 'min:8'];
        }

        $this->validate($rules);

        if ($this->editingUserId) {
            $data = ['name' => $this->userName, 'email' => $this->userEmail, 'role' => $this->userRole];
            if ($this->userPassword) {
                $data['password'] = Hash::make($this->userPassword);
            }
            User::findOrFail($this->editingUserId)->update($data);
            session()->flash('success', 'Data pengguna berhasil diperbarui.');
        } else {
            User::create([
                'name' => $this->userName,
                'email' => $this->userEmail,
                'role' => $this->userRole,
                'password' => Hash::make($this->userPassword),
                'is_active' => true,
            ]);
            session()->flash('success', 'Pengguna baru berhasil ditambahkan.');
        }

        $this->resetUserForm();
    }

    public function cancelUserForm(): void
    {
        $this->resetUserForm();
    }

    public function toggleActive(int $userId): void
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);

        $user = User::findOrFail($userId);

        if ($user->id === auth('admin')->id()) {
            return;
        }

        $user->update(['is_active' => ! $user->is_active]);
    }

    private function resetUserForm(): void
    {
        $this->showUserForm = false;
        $this->editingUserId = null;
        $this->userName = '';
        $this->userEmail = '';
        $this->userRole = 'admin';
        $this->userPassword = '';
        $this->resetValidation();
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
