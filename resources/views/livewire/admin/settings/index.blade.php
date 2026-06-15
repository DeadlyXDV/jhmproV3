<div x-data="{ tab: @entangle('activeTab') }">

    @if($showSuccessMessage)
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-xl flex items-center gap-2 text-sm text-green-700"
            x-init="setTimeout(() => $wire.set('showSuccessMessage', false), 3000)">
            <x-heroicon-o-check-circle class="w-5 h-5 flex-none" />
            Perubahan berhasil disimpan.
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-1 mb-6 bg-white rounded-2xl border border-gray-100 p-1 w-fit shadow-sm">
        @foreach([
            'profil' => 'Profil Bengkel',
            'users' => 'Manajemen User',
            'booking' => 'Config Booking',
            'rfm' => 'Config RFM',
        ] as $key => $label)
            <button wire:click="$set('activeTab', '{{ $key }}')"
                class="px-5 py-2 text-sm font-semibold rounded-xl transition-colors
                    {{ $activeTab === $key ? 'bg-red-600 text-white shadow' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Tab: Profil Bengkel --}}
    @if($activeTab === 'profil')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-2xl">
            <h3 class="text-sm font-semibold text-gray-700 mb-5">Profil Bengkel</h3>
            <form wire:submit="saveProfil" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Bengkel</label>
                    <input wire:model="namaBengkel" type="text"
                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
                    @error('namaBengkel') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tagline</label>
                    <input wire:model="tagline" type="text"
                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat</label>
                    <textarea wire:model="alamat" rows="3"
                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none resize-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">No. HP</label>
                        <input wire:model="noHp" type="text"
                            class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                        <input wire:model="email" type="email"
                            class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
                        @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jam Buka</label>
                        <input wire:model="jamBuka" type="time"
                            class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jam Tutup</label>
                        <input wire:model="jamTutup" type="time"
                            class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
                    </div>
                </div>
                <div class="pt-2">
                    <button type="submit"
                        class="px-5 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition-colors">
                        Simpan Profil
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Tab: Manajemen User --}}
    @if($activeTab === 'users')
        <div class="space-y-4">
            {{-- Form Tambah/Edit User --}}
            @if($showUserForm)
                <div class="bg-white rounded-2xl border-2 border-red-100 shadow-sm p-6 max-w-2xl">
                    <h3 class="text-sm font-semibold text-gray-700 mb-5">
                        {{ $editingUserId ? 'Edit User' : 'Tambah User Baru' }}
                    </h3>
                    <form wire:submit="saveUser" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Lengkap</label>
                                <input wire:model="userName" type="text"
                                    class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
                                @error('userName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                                <input wire:model="userEmail" type="email"
                                    class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
                                @error('userEmail') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Role</label>
                                <select wire:model="userRole"
                                    class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none bg-white">
                                    <option value="admin">Admin</option>
                                    <option value="mekanik">Mekanik</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Password {{ $editingUserId ? '(kosongkan jika tidak diubah)' : '' }}
                                </label>
                                <input wire:model="userPassword" type="password"
                                    class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
                                @error('userPassword') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="flex gap-2 pt-2">
                            <button type="submit"
                                class="px-5 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition-colors">
                                {{ $editingUserId ? 'Simpan Perubahan' : 'Buat Akun' }}
                            </button>
                            <button type="button" wire:click="cancelUserForm"
                                class="px-5 py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- User List --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="relative">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                        <input wire:model.live.debounce.300ms="userSearch"
                            type="text" placeholder="Cari nama atau email..."
                            class="pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
                    </div>
                    @if(!$showUserForm)
                        <button wire:click="openUserCreate"
                            class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition-colors">
                            <x-heroicon-o-plus class="w-4 h-4" />
                            Tambah User
                        </button>
                    @endif
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50">
                            <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Nama</th>
                            <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Email</th>
                            <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Role</th>
                            <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Status</th>
                            <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center text-white text-xs font-bold flex-none">
                                            {{ $user->initials() }}
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $roleColor = match($user->role) {
                                            'super_admin' => 'bg-red-100 text-red-700',
                                            'admin' => 'bg-blue-100 text-blue-700',
                                            'mekanik' => 'bg-green-100 text-green-700',
                                            default => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold {{ $roleColor }}">
                                        {{ str_replace('_', ' ', ucfirst($user->role)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button wire:click="toggleUserActive({{ $user->id }})"
                                        @if($user->id === auth('admin')->id()) disabled @endif
                                        class="flex items-center gap-1.5 text-xs font-semibold {{ $user->is_active ? 'text-green-600' : 'text-gray-400' }}
                                            {{ $user->id !== auth('admin')->id() ? 'hover:opacity-70 transition-opacity cursor-pointer' : 'cursor-default opacity-50' }}">
                                        <span class="w-2 h-2 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->id !== auth('admin')->id() && $user->role !== 'super_admin')
                                        <button wire:click="openUserEdit({{ $user->id }})"
                                            class="text-xs font-semibold text-gray-500 hover:text-gray-800 transition-colors">
                                            Edit
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <p class="text-sm text-gray-400">Belum ada user</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Tab: Config Booking --}}
    @if($activeTab === 'booking')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-2xl">
            <h3 class="text-sm font-semibold text-gray-700 mb-5">Konfigurasi Booking</h3>
            <form wire:submit="saveBookingConfig" class="space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kapasitas Slot Per Hari</label>
                        <input wire:model="bookingKapasitas" type="number" min="1" max="20"
                            class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
                        @error('bookingKapasitas') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Maks. Advance Booking (hari)</label>
                        <input wire:model="bookingAdvanceDays" type="number" min="1" max="365"
                            class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
                        @error('bookingAdvanceDays') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-3">Hari Operasional</label>
                    <div class="flex gap-2 flex-wrap">
                        @foreach([1 => 'Sen', 2 => 'Sel', 3 => 'Rab', 4 => 'Kam', 5 => 'Jum', 6 => 'Sab', 7 => 'Min'] as $num => $label)
                            <label class="cursor-pointer">
                                <input type="checkbox" wire:model="bookingHari.{{ $num }}" class="sr-only peer" />
                                <span class="flex items-center justify-center w-12 h-10 rounded-xl text-xs font-bold border-2 transition-all
                                    peer-checked:bg-red-600 peer-checked:border-red-600 peer-checked:text-white
                                    border-gray-200 text-gray-500 hover:border-gray-300">
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="px-5 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition-colors">
                        Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Tab: Config Segmen RFM --}}
    @if($activeTab === 'rfm')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-3xl">
            <h3 class="text-sm font-semibold text-gray-700 mb-5">Konfigurasi Segmen RFM</h3>

            @if($clusters)
                <form wire:submit="saveClusters" class="space-y-4">
                    @foreach($clusters as $id => $cluster)
                        <div class="flex items-start gap-4 p-4 border border-gray-100 rounded-xl">
                            <div class="flex-none">
                                <input type="color" wire:model="clusters.{{ $id }}.color_hex"
                                    class="w-10 h-10 rounded-xl border border-gray-200 cursor-pointer p-0.5" />
                            </div>
                            <div class="flex-1 grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Label Segmen</label>
                                    <input wire:model="clusters.{{ $id }}.label" type="text"
                                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Deskripsi</label>
                                    <input wire:model="clusters.{{ $id }}.description" type="text"
                                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none" />
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="pt-2">
                        <button type="submit"
                            class="px-5 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition-colors">
                            Simpan Konfigurasi Segmen
                        </button>
                    </div>
                </form>
            @else
                <div class="text-center py-12">
                    <x-heroicon-o-chart-bar class="w-10 h-10 text-gray-300 mx-auto mb-3" />
                    <p class="text-sm text-gray-400">Belum ada cluster RFM yang terdefinisi</p>
                    <p class="text-xs text-gray-400 mt-1">Jalankan job RFM pertama untuk mengisi cluster definitions</p>
                </div>
            @endif
        </div>
    @endif
</div>
