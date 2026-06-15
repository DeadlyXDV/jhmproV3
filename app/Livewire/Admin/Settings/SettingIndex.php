<?php

namespace App\Livewire\Admin\Settings;

use App\Models\ClusterDefinition;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Livewire\Component;

class SettingIndex extends Component
{
    public string $activeTab = 'profil';

    // Profil Bengkel
    public string $namaBengkel = '';

    public string $tagline = '';

    public string $alamat = '';

    public string $noHp = '';

    public string $email = '';

    public string $jamBuka = '';

    public string $jamTutup = '';

    // Manajemen User
    public string $userSearch = '';

    public bool $showUserForm = false;

    public ?int $editingUserId = null;

    public string $userName = '';

    public string $userEmail = '';

    public string $userRole = 'admin';

    public string $userPassword = '';

    // Config Booking
    public int $bookingKapasitas = 2;

    public int $bookingAdvanceDays = 30;

    /** @var array<int, bool> */
    public array $bookingHari = [1 => true, 2 => true, 3 => true, 4 => true, 5 => true, 6 => true, 7 => false];

    // Config RFM Cluster
    /** @var array<int, array{label: string, description: string, color_hex: string}> */
    public array $clusters = [];

    public bool $showSuccessMessage = false;

    public function mount(): void
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);

        $this->namaBengkel = Setting::get('nama_bengkel', 'JHMPro');
        $this->tagline = Setting::get('tagline', '');
        $this->alamat = Setting::get('alamat', '');
        $this->noHp = Setting::get('no_hp', '');
        $this->email = Setting::get('email', '');
        $this->jamBuka = Setting::get('jam_buka', '08:00');
        $this->jamTutup = Setting::get('jam_tutup', '17:00');

        $this->bookingKapasitas = (int) Setting::get('booking_kapasitas', 2);
        $this->bookingAdvanceDays = (int) Setting::get('booking_advance_days', 30);

        $savedHari = Setting::get('booking_hari');
        if ($savedHari) {
            $this->bookingHari = json_decode($savedHari, true);
        }

        foreach (ClusterDefinition::orderBy('id')->get() as $cluster) {
            $this->clusters[$cluster->id] = [
                'label' => $cluster->label,
                'description' => $cluster->description ?? '',
                'color_hex' => $cluster->color_hex,
            ];
        }
    }

    public function saveProfil(): void
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);

        $this->validate([
            'namaBengkel' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'noHp' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'jamBuka' => 'nullable|date_format:H:i',
            'jamTutup' => 'nullable|date_format:H:i',
        ]);

        Setting::set('nama_bengkel', $this->namaBengkel);
        Setting::set('tagline', $this->tagline);
        Setting::set('alamat', $this->alamat);
        Setting::set('no_hp', $this->noHp);
        Setting::set('email', $this->email);
        Setting::set('jam_buka', $this->jamBuka);
        Setting::set('jam_tutup', $this->jamTutup);

        $this->showSuccessMessage = true;
    }

    public function saveBookingConfig(): void
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);

        $this->validate([
            'bookingKapasitas' => 'required|integer|min:1|max:20',
            'bookingAdvanceDays' => 'required|integer|min:1|max:365',
        ]);

        Setting::set('booking_kapasitas', (string) $this->bookingKapasitas);
        Setting::set('booking_advance_days', (string) $this->bookingAdvanceDays);
        Setting::set('booking_hari', json_encode($this->bookingHari));

        $this->showSuccessMessage = true;
    }

    public function saveClusters(): void
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);

        foreach ($this->clusters as $id => $data) {
            $cluster = ClusterDefinition::find($id);
            if (! $cluster) {
                continue;
            }
            $cluster->update([
                'label' => $data['label'],
                'description' => $data['description'],
                'color_hex' => $data['color_hex'],
            ]);
        }

        $this->showSuccessMessage = true;
    }

    public function openUserCreate(): void
    {
        $this->resetUserForm();
        $this->showUserForm = true;
    }

    public function openUserEdit(int $userId): void
    {
        $user = User::findOrFail($userId);
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
            'userName' => 'required|string|max:255',
            'userEmail' => 'required|email|unique:users,email'.($this->editingUserId ? ",{$this->editingUserId}" : ''),
            'userRole' => 'required|in:admin,mekanik',
        ];

        if (! $this->editingUserId) {
            $rules['userPassword'] = 'required|string|min:8';
        }

        $this->validate($rules);

        if ($this->editingUserId) {
            $data = ['name' => $this->userName, 'email' => $this->userEmail, 'role' => $this->userRole];
            if ($this->userPassword) {
                $data['password'] = Hash::make($this->userPassword);
            }
            User::findOrFail($this->editingUserId)->update($data);
        } else {
            User::create([
                'name' => $this->userName,
                'email' => $this->userEmail,
                'role' => $this->userRole,
                'password' => Hash::make($this->userPassword),
                'is_active' => true,
            ]);
        }

        $this->resetUserForm();
        $this->showSuccessMessage = true;
    }

    public function toggleUserActive(int $userId): void
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);

        $user = User::findOrFail($userId);

        if ($user->id === auth('admin')->id()) {
            return;
        }

        $user->update(['is_active' => ! $user->is_active]);
    }

    public function cancelUserForm(): void
    {
        $this->resetUserForm();
    }

    private function resetUserForm(): void
    {
        $this->showUserForm = false;
        $this->editingUserId = null;
        $this->userName = '';
        $this->userEmail = '';
        $this->userRole = 'admin';
        $this->userPassword = '';
        $this->showSuccessMessage = false;
        $this->resetValidation();
    }

    public function render(): View
    {
        $users = User::query()
            ->whereIn('role', ['super_admin', 'admin', 'mekanik'])
            ->when($this->userSearch, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('name', 'like', "%{$this->userSearch}%")
                        ->orWhere('email', 'like', "%{$this->userSearch}%");
                });
            })
            ->latest()
            ->get();

        $clusterDefinitions = ClusterDefinition::orderBy('id')->get();

        return view('livewire.admin.settings.index', compact('users', 'clusterDefinitions'))
            ->layout('layouts.admin', ['title' => 'Pengaturan']);
    }
}
