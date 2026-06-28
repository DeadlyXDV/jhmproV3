<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Pengguna</h1>
            <p class="text-sm text-gray-500 mt-0.5">Daftar staf & admin sistem</p>
        </div>
        @if(auth('admin')->user()?->isSuperAdmin())
        <button wire:click="openUserCreate"
                class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <x-heroicon-o-plus class="w-4 h-4" />
            Tambah Pengguna
        </button>
        @endif
    </div>

    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    {{-- Inline Form --}}
    @if($showUserForm)
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-red-100">
        <h2 class="text-base font-semibold text-gray-900 mb-5">
            {{ $editingUserId ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}
        </h2>
        <form wire:submit="saveUser" class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                <input wire:model="userName" type="text"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 @error('userName') border-red-500 @enderror"
                       placeholder="John Doe" />
                @error('userName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input wire:model="userEmail" type="email"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 @error('userEmail') border-red-500 @enderror"
                       placeholder="john@jhmpro.test" />
                @error('userEmail') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                <select wire:model="userRole"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 bg-white @error('userRole') border-red-500 @enderror">
                    <option value="admin">Admin</option>
                    <option value="mekanik">Mekanik</option>
                </select>
                @error('userRole') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Password {{ $editingUserId ? '(kosongkan jika tidak diubah)' : '*' }}
                </label>
                <input wire:model="userPassword" type="password"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 @error('userPassword') border-red-500 @enderror"
                       placeholder="{{ $editingUserId ? '••••••••' : 'Min. 8 karakter' }}" />
                @error('userPassword') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2 flex items-center gap-3">
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition flex items-center gap-2">
                    <div wire:loading wire:target="saveUser" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    <span wire:loading.remove wire:target="saveUser">Simpan</span>
                    <span wire:loading wire:target="saveUser">Menyimpan...</span>
                </button>
                <button type="button" wire:click="cancelUserForm"
                        class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2">Batal</button>
            </div>
        </form>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            <div class="relative flex-1 max-w-xs">
                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Cari nama, email..."
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
            </div>

            <select wire:model.live="filterRole"
                    class="border border-gray-300 text-gray-700 text-sm px-3 py-2 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Semua Role</option>
                <option value="super_admin">Super Admin</option>
                <option value="admin">Admin</option>
                <option value="mekanik">Mekanik</option>
            </select>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                    <th class="text-left px-5 py-3">Nama</th>
                    <th class="text-left px-5 py-3">Email</th>
                    <th class="text-left px-5 py-3">Role</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                @php
                    $colors = ['bg-red-100 text-red-700','bg-blue-100 text-blue-700','bg-green-100 text-green-700','bg-purple-100 text-purple-700','bg-amber-100 text-amber-700'];
                    $avatarColor = $colors[abs(crc32($user->name)) % count($colors)];
                    $initials = collect(explode(' ', $user->name))->take(2)->map(fn($w) => strtoupper($w[0]))->join('');
                    [$roleDot, $roleLabel] = match($user->role) {
                        'super_admin' => ['bg-red-600', 'Super Admin'],
                        'admin'       => ['bg-amber-500', 'Admin'],
                        'mekanik'     => ['bg-blue-500', 'Mekanik'],
                        default       => ['bg-gray-400', $user->role],
                    };
                @endphp
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 {{ $avatarColor }}">
                                {{ $initials }}
                            </div>
                            <span class="font-medium text-gray-900">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-600">{{ $user->email }}</td>
                    <td class="px-5 py-4">
                        <span class="flex items-center gap-1.5 text-xs font-medium text-gray-700">
                            <span class="w-2 h-2 rounded-full {{ $roleDot }}"></span>{{ $roleLabel }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        @if($user->is_active)
                        <span class="flex items-center gap-1.5 text-xs font-medium text-green-700">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>Aktif
                        </span>
                        @else
                        <span class="flex items-center gap-1.5 text-xs font-medium text-red-700">
                            <span class="w-2 h-2 rounded-full bg-red-600"></span>Nonaktif
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if(auth('admin')->user()?->isSuperAdmin() && $user->id !== auth('admin')->id() && $user->role !== 'super_admin')
                        <div class="flex items-center gap-2 justify-end">
                            <button wire:click="openUserEdit({{ $user->id }})"
                                    class="text-xs px-2 py-1 rounded border text-gray-600 border-gray-300 hover:bg-gray-50 transition">
                                Edit
                            </button>
                            <button wire:click="toggleActive({{ $user->id }})"
                                    wire:confirm="Apakah Anda yakin ingin mengubah status aktif pengguna ini?"
                                    class="text-xs px-2 py-1 rounded border text-gray-600 border-gray-300 hover:bg-gray-50 transition">
                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-sm text-gray-400">
                        Tidak ada pengguna ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($users->hasPages())
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
            <p class="text-sm text-gray-500">
                Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }}
            </p>
            <div class="flex items-center gap-1">
                @if($users->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Sebelumnya</span>
                @else
                <button wire:click="previousPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Sebelumnya</button>
                @endif
                @if($users->hasMorePages())
                <button wire:click="nextPage" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Selanjutnya</button>
                @else
                <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded cursor-not-allowed">Selanjutnya</span>
                @endif
            </div>
        </div>
        @endif

    </div>
</div>
