<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Mekanik' }} — JHMPro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-[#F3F4F6] font-[Inter,ui-sans-serif,system-ui]">

    {{-- Sidebar mekanik (lebih minimal) --}}
    <aside class="fixed inset-y-0 left-0 z-40 w-52 bg-[#111827] flex flex-col">

        <div class="flex items-center gap-2 px-4 h-16 border-b border-white/10 flex-shrink-0">
            <div class="w-7 h-7 bg-red-600 rounded-md flex items-center justify-center">
                <x-heroicon-s-wrench-screwdriver class="w-4 h-4 text-white" />
            </div>
            <span class="text-white font-bold text-sm tracking-wide">JHMPro</span>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">
            <p class="px-2 mb-1 mt-2 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Menu</p>

            <x-admin.nav-item href="{{ route('mekanik.dashboard') }}" icon="o-squares-2x2" :active="request()->routeIs('mekanik.dashboard')">
                Dashboard
            </x-admin.nav-item>

            <x-admin.nav-item href="{{ route('mekanik.work-orders.index') }}" icon="o-bolt" :active="request()->routeIs('mekanik.work-orders.*')">
                Work Order Saya
            </x-admin.nav-item>
        </nav>

        <div class="p-3 border-t border-white/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-400 hover:bg-white/10 hover:text-white transition-colors">
                    <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4" />
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="ml-52 flex flex-col min-h-screen">

        <header class="fixed top-0 left-52 right-0 z-30 h-16 bg-white border-b border-gray-200 flex items-center px-6 gap-4">
            <div class="flex-1">
                @isset($header)
                    {{ $header }}
                @endisset
            </div>

            <div class="flex items-center gap-3">
                {{-- Toggle available --}}
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <span>Status:</span>
                    @if(auth('admin')->user()?->is_available)
                        <span class="inline-flex items-center gap-1.5 text-green-700">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            Online
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-gray-500">
                            <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                            Offline
                        </span>
                    @endif
                </div>

                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-700 text-xs font-bold">
                    {{ auth('admin')->user()?->initials() }}
                </div>
                <span class="hidden sm:block text-sm font-medium text-gray-700">{{ auth('admin')->user()?->name }}</span>
            </div>
        </header>

        <main class="mt-16 p-6 flex-1">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
