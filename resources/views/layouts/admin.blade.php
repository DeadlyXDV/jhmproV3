<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} — JHMPro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-[#F3F4F6] font-[Inter,ui-sans-serif,system-ui]"
    x-data="{ 
        collapsed: localStorage.getItem('sidebar-collapsed') === 'true',
        toggle() {
            this.collapsed = !this.collapsed;
            localStorage.setItem('sidebar-collapsed', this.collapsed);
        }
    }"
    @toggle-sidebar.window="toggle()">

    {{-- Sidebar --}}
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 z-40 bg-[#14161b] flex flex-col overflow-hidden transition-all duration-300"
        :class="collapsed ? 'w-20' : 'w-64'">

        {{-- Logo --}}
        <div class="flex items-center gap-3 h-16 flex-shrink-0"
            :class="collapsed ? 'px-0 justify-center' : 'px-6'">
            <div class="w-10 h-10 bg-[#E11D22] rounded-[12px] flex items-center justify-center shadow-lg shadow-red-600/20 flex-none">
                <svg viewBox="0 0 40 42" class="w-6 h-6 text-white"><path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M17.2 5.633 8.6.855 0 5.633v26.51l16.2 9 16.2-9v-8.442l7.6-4.223V9.856l-8.6-4.777-8.6 4.777V18.3l-5.6 3.111V5.633ZM38 18.301l-5.6 3.11v-6.157l5.6-3.11V18.3Zm-1.06-7.856-5.54 3.078-5.54-3.079 5.54-3.078 5.54 3.079ZM24.8 18.3v-6.157l5.6 3.111v6.158L24.8 18.3Zm-1 1.732 5.54 3.078-13.14 7.302-5.54-3.078 13.14-7.3v-.002Zm-16.2 7.89 7.6 4.222V38.3L2 30.966V7.92l5.6 3.111v16.892ZM8.6 9.3 3.06 6.222 8.6 3.143l5.54 3.08L8.6 9.3Zm21.8 15.51-13.2 7.334V38.3l13.2-7.334v-6.156ZM9.6 11.034l5.6-3.11v14.6l-5.6 3.11v-14.6Z" /></svg>
            </div>
            <span x-show="!collapsed" x-cloak class="text-white font-bold text-[20px] tracking-tight whitespace-nowrap overflow-hidden">
                JHM<span class="text-[#E11D22]">Pro</span>
            </span>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto py-2 space-y-1 custom-scrollbar"
            :class="collapsed ? 'px-2' : 'px-4'">

            @php
                $menuGroups = [
                    'MENU' => [
                        ['route' => 'admin.dashboard', 'icon' => 'o-home', 'label' => 'Dashboard', 'base' => 'admin.dashboard'],
                        ['route' => 'admin.customers.index', 'icon' => 'o-users', 'label' => 'Pelanggan', 'base' => 'admin.customers'],
                        ['route' => 'admin.vehicles.index', 'icon' => 'o-truck', 'label' => 'Kendaraan', 'base' => 'admin.vehicles'],
                        ['route' => 'admin.invoices.index', 'icon' => 'o-document-text', 'label' => 'Invoice', 'base' => 'admin.invoices'],
                        ['route' => 'admin.bookings.index', 'icon' => 'o-calendar', 'label' => 'Booking', 'base' => 'admin.bookings'],
                        ['route' => 'admin.services.index', 'icon' => 'o-sun', 'label' => 'Servis', 'base' => 'admin.services'],
                        ['route' => 'admin.work-orders.index', 'icon' => 'o-clipboard-document-list', 'label' => 'Work Order', 'base' => 'admin.work-orders'],
                        ['route' => 'admin.partners.index', 'icon' => 'o-building-office-2', 'label' => 'Partner', 'base' => 'admin.partners'],
                        ['route' => 'admin.product-bundles.index', 'icon' => 'o-cube', 'label' => 'Paket Produk', 'base' => 'admin.product-bundles'],
                        ['route' => 'admin.users.index', 'icon' => 'o-user', 'label' => 'Pengguna', 'base' => 'admin.users'],
                    ],
                    'INVENTORY' => [
                        ['route' => 'admin.spareparts.index', 'icon' => 'o-wrench', 'label' => 'Sparepart', 'base' => 'admin.spareparts'],
                        ['route' => 'admin.sparepart-categories.index', 'icon' => 'o-tag', 'label' => 'Kategori', 'base' => 'admin.sparepart-categories'],
                        ['route' => 'admin.stock-movements.index', 'icon' => 'o-arrows-right-left', 'label' => 'Stok', 'base' => 'admin.stock-movements'],
                    ],
                ];

                if (auth('admin')->user()?->isSuperAdmin()) {
                    $menuGroups['ANALITIK'] = [
                        ['route' => 'admin.orders.index', 'icon' => 'o-shopping-bag', 'label' => 'Orders', 'base' => 'admin.orders'],
                        ['route' => 'admin.rfm.index', 'icon' => 'o-chart-bar', 'label' => 'RFM', 'base' => 'admin.rfm'],
                        ['route' => 'admin.reports.index', 'icon' => 'o-banknotes', 'label' => 'Laporan', 'base' => 'admin.reports'],
                        ['route' => 'admin.settings.index', 'icon' => 'o-cog-6-tooth', 'label' => 'Pengaturan', 'base' => 'admin.settings'],
                    ];
                }
            @endphp

            @foreach($menuGroups as $group => $items)
                <p x-show="!collapsed" x-cloak
                    class="mb-2 text-[11px] font-bold uppercase tracking-wider text-gray-500 whitespace-nowrap overflow-hidden px-2"
                    :class="{{ $loop->first ? "'mt-0'" : "'mt-8'" }}">
                    {{ $group }}
                </p>

                @foreach($items as $item)
                    <x-admin.nav-item
                        href="{{ route($item['route']) }}"
                        icon="{{ $item['icon'] }}"
                        :active="request()->routeIs($item['base'] . '*')"
                    >
                        {{ $item['label'] }}
                    </x-admin.nav-item>
                @endforeach
            @endforeach

        </nav>
    </aside>

    {{-- Main area --}}
    <div class="flex flex-col min-h-screen"
        :class="collapsed ? 'ml-20' : 'ml-64'">

        {{-- Topbar --}}
        <header class="fixed top-0 right-0 z-30 h-20 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center px-8 gap-6"
            :class="collapsed ? 'left-20' : 'left-64'">
            {{-- Menu Toggle --}}
            <button @click="$dispatch('toggle-sidebar')" class="p-2.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-xl cursor-pointer">
                <x-heroicon-o-bars-3-bottom-left class="w-6 h-6" :class="collapsed ? 'rotate-180' : ''" />
            </button>

            {{-- Breadcrumb / title --}}
            <div class="flex-1">
                @if(isset($breadcrumbs))
                    {{ $breadcrumbs }}
                @else
                    <nav class="flex items-center gap-2 text-[13px] text-gray-400 mb-0.5">
                        <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-gray-600 transition-colors">Beranda</a>
                        <x-heroicon-m-chevron-right class="w-3 h-3" />
                        <span class="text-gray-500">{{ $title ?? 'Dashboard' }}</span>
                    </nav>
                @endif
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">{{ $title ?? 'Dashboard' }}</h1>
            </div>

            {{-- Search Bar --}}
            <div class="hidden lg:flex items-center flex-1 max-w-md relative group">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-4 text-gray-400 group-focus-within:text-red-500 transition-colors" />
                <input type="text"
                    placeholder="Cari booking, sparepart, pelanggan"
                    class="w-full bg-gray-100 border-none rounded-xl py-2.5 pl-11 pr-14 text-sm focus:ring-2 focus:ring-red-500/20 placeholder:text-gray-400">
                <div class="absolute right-3 px-1.5 py-0.5 bg-white border border-gray-200 rounded text-[10px] font-medium text-gray-400 flex items-center gap-0.5">
                    <span class="text-xs">⌘</span>K
                </div>
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-5">
                {{-- Actions --}}
                <div class="flex items-center gap-2">
                    <button class="p-2 text-gray-400 hover:bg-gray-50 rounded-full transition-colors cursor-pointer">
                        <x-heroicon-o-moon class="w-5 h-5" />
                    </button>
                    <button class="p-2 text-gray-400 hover:bg-gray-50 rounded-full transition-colors relative cursor-pointer">
                        <x-heroicon-o-bell class="w-5 h-5" />
                        <span class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 border-2 border-white rounded-full"></span>
                    </button>
                </div>

                <div class="h-8 w-px bg-gray-100"></div>

                {{-- User profile --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center gap-3 p-1 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer">
                        <div class="w-10 h-10 rounded-full bg-red-600 shadow-lg shadow-red-600/20 flex items-center justify-center text-white text-sm font-bold ring-4 ring-red-50/50">
                            {{ auth('admin')->user()?->initials() }}
                        </div>
                        <div class="hidden sm:block text-left leading-tight">
                            <p class="text-sm font-bold text-gray-900">{{ auth('admin')->user()?->name }}</p>
                            <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">{{ auth('admin')->user()?->role }}</p>
                        </div>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-cloak
                        class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">

                        @if(auth('admin')->user()?->isAdmin())
                        <a href="{{ route('admin.pos') }}" wire:navigate
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-white bg-red-600 mx-2 mb-2 rounded-xl shadow-lg shadow-red-600/20 hover:bg-red-700 transition-colors">
                            <x-heroicon-o-calculator class="w-5 h-5" />
                            Buka Kasir POS
                        </a>
                        <div class="border-t border-gray-100 my-2"></div>
                        @endif

                        <a href="{{ route('admin.settings.index') }}" wire:navigate
                            class="flex items-center gap-3 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                            <x-heroicon-o-cog-6-tooth class="w-5 h-5 text-gray-400" />
                            Pengaturan Profil
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 hover:text-red-700 cursor-pointer">
                                <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                                Keluar Sistem
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="mt-20 p-8 flex-1">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
