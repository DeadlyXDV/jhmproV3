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
<body class="h-full bg-[#F3F4F6] font-[Inter,ui-sans-serif,system-ui]">

    {{-- Sidebar --}}
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 z-40 w-52 bg-[#111827] flex flex-col transition-transform duration-200"
        x-data="{ open: true }">

        {{-- Logo --}}
        <div class="flex items-center gap-2 px-4 h-16 border-b border-white/10 flex-shrink-0">
            <div class="w-7 h-7 bg-red-600 rounded-md flex items-center justify-center">
                <x-heroicon-s-wrench-screwdriver class="w-4 h-4 text-white" />
            </div>
            <span class="text-white font-bold text-sm tracking-wide">JHMPro</span>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">

            {{-- MENU --}}
            <p class="px-2 mb-1 mt-2 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Menu</p>

            <x-admin.nav-item href="{{ route('admin.dashboard') }}" icon="o-squares-2x2" :active="request()->routeIs('admin.dashboard')">
                Dashboard
            </x-admin.nav-item>

            <x-admin.nav-item href="{{ route('admin.customers.index') }}" icon="o-users" :active="request()->routeIs('admin.customers.*')">
                Pelanggan
            </x-admin.nav-item>

            <x-admin.nav-item href="{{ route('admin.vehicles.index') }}" icon="o-wrench-screwdriver" :active="request()->routeIs('admin.vehicles.*')">
                Kendaraan
            </x-admin.nav-item>

            <x-admin.nav-item href="{{ route('admin.invoices.index') }}" icon="o-document-text" :active="request()->routeIs('admin.invoices.*')">
                Invoice
            </x-admin.nav-item>

            <x-admin.nav-item href="{{ route('admin.bookings.index') }}" icon="o-calendar-days" :active="request()->routeIs('admin.bookings.*')">
                Booking
            </x-admin.nav-item>

            <x-admin.nav-item href="{{ route('admin.services.index') }}" icon="o-clipboard-document-list" :active="request()->routeIs('admin.services.*')">
                Servis
            </x-admin.nav-item>

            <x-admin.nav-item href="{{ route('admin.work-orders.index') }}" icon="o-bolt" :active="request()->routeIs('admin.work-orders.*')">
                Work Order
            </x-admin.nav-item>

            <x-admin.nav-item href="{{ route('admin.partners.index') }}" icon="o-building-storefront" :active="request()->routeIs('admin.partners.*')">
                Partner
            </x-admin.nav-item>

            <x-admin.nav-item href="{{ route('admin.product-bundles.index') }}" icon="o-cube" :active="request()->routeIs('admin.product-bundles.*')">
                Paket Produk
            </x-admin.nav-item>

            <x-admin.nav-item href="{{ route('admin.users.index') }}" icon="o-user-group" :active="request()->routeIs('admin.users.*')">
                Pengguna
            </x-admin.nav-item>

            {{-- INVENTORY --}}
            <p class="px-2 mb-1 mt-5 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Inventory</p>

            <x-admin.nav-item href="{{ route('admin.spareparts.index') }}" icon="o-tag" :active="request()->routeIs('admin.spareparts.*')">
                Sparepart
            </x-admin.nav-item>

            <x-admin.nav-item href="{{ route('admin.sparepart-categories.index') }}" icon="o-folder" :active="request()->routeIs('admin.sparepart-categories.*')">
                Kategori Sparepart
            </x-admin.nav-item>

            <x-admin.nav-item href="{{ route('admin.stock-movements.index') }}" icon="o-arrow-path" :active="request()->routeIs('admin.stock-movements.*')">
                Pergerakan Stok
            </x-admin.nav-item>

            {{-- super_admin only --}}
            @if(auth('admin')->user()?->isSuperAdmin())
            <p class="px-2 mb-1 mt-5 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Analitik</p>

            <x-admin.nav-item href="{{ route('admin.orders.index') }}" icon="o-shopping-bag" :active="request()->routeIs('admin.orders.*')">
                Orders
            </x-admin.nav-item>

            <x-admin.nav-item href="{{ route('admin.rfm.index') }}" icon="o-chart-bar" :active="request()->routeIs('admin.rfm.*')">
                Segmentasi RFM
            </x-admin.nav-item>

            <x-admin.nav-item href="{{ route('admin.reports.index') }}" icon="o-banknotes" :active="request()->routeIs('admin.reports.*')">
                Laporan Keuangan
            </x-admin.nav-item>

            <x-admin.nav-item href="{{ route('admin.settings.index') }}" icon="o-cog-6-tooth" :active="request()->routeIs('admin.settings.*')">
                Pengaturan
            </x-admin.nav-item>
            @endif

        </nav>
    </aside>

    {{-- Main area --}}
    <div class="ml-52 flex flex-col min-h-screen">

        {{-- Topbar --}}
        <header class="fixed top-0 left-52 right-0 z-30 h-16 bg-white border-b border-gray-200 flex items-center px-6 gap-4">
            {{-- Breadcrumb / title --}}
            <div class="flex-1 min-w-0">
                @isset($header)
                    {{ $header }}
                @endisset
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-3">
                {{-- User dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-700 text-xs font-bold">
                            {{ auth('admin')->user()?->initials() }}
                        </div>
                        <span class="hidden sm:block">{{ auth('admin')->user()?->name }}</span>
                        <x-heroicon-o-chevron-down class="w-4 h-4 text-gray-400" />
                    </button>

                    <div x-show="open" @click.outside="open = false" x-cloak
                        class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">

                        @if(auth('admin')->user()?->isAdmin())
                        <a href="{{ route('admin.pos') }}"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-red-600 mx-2 my-1 rounded-lg">
                            <x-heroicon-o-calculator class="w-4 h-4" />
                            Buka Kasir POS
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        @endif

                        <a href="{{ route('admin.settings.index') }}"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <x-heroicon-o-cog-6-tooth class="w-4 h-4 text-gray-400" />
                            Pengaturan
                        </a>

                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4 text-gray-400" />
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="mt-16 p-6 flex-1">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
