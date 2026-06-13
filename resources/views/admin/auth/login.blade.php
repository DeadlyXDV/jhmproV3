<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin — JHMPro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-[Inter,ui-sans-serif,system-ui]">

<div class="h-full flex">
    {{-- Kiri — dark panel --}}
    <div class="hidden lg:flex lg:w-1/2 bg-[#111827] flex-col justify-between p-12">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center">
                <x-heroicon-s-wrench-screwdriver class="w-5 h-5 text-white" />
            </div>
            <span class="text-white font-bold text-lg">JHMPro</span>
        </div>

        <div>
            <h1 class="text-3xl font-bold text-white leading-snug mb-4">
                Dari keluhan pertama,<br>hingga nota terakhir.
            </h1>
            <ul class="space-y-3">
                @foreach(['Work order & booking servis', 'Invoice & kasir terintegrasi', 'Stok sparepart real-time'] as $item)
                <li class="flex items-center gap-3 text-gray-400 text-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0"></span>
                    {{ $item }}
                </li>
                @endforeach
            </ul>
        </div>

        <p class="text-gray-600 text-xs">JHMPro Workshop Management System</p>
    </div>

    {{-- Kanan — form --}}
    <div class="flex-1 flex items-center justify-center p-8 bg-white">
        <div class="w-full max-w-sm">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Masuk ke Panel Admin</h2>
                <p class="text-sm text-gray-500 mt-1">Gunakan akun admin atau mekanik kamu</p>
            </div>

            @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-600">{{ $errors->first() }}</p>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Alamat Email
                    </label>
                    <input
                        id="email" name="email" type="email"
                        value="{{ old('email') }}"
                        required autofocus autocomplete="email"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition @error('email') border-red-500 @enderror"
                        placeholder="admin@jhmpro.id"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <div class="relative" x-data="{ show: false }">
                        <input
                            id="password" name="password"
                            :type="show ? 'text' : 'password'"
                            required autocomplete="current-password"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm pr-10 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition"
                            placeholder="Password"
                        >
                        <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <x-heroicon-o-eye x-show="!show" class="w-4 h-4" />
                            <x-heroicon-o-eye-slash x-show="show" x-cloak class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        Ingat saya
                    </label>
                </div>

                <button type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-lg text-sm transition-colors">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</div>

@vite(['resources/js/app.js'])
</body>
</html>
