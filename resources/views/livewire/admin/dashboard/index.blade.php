<div class="space-y-8">
    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Card 1: Pendapatan --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-40 group hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="p-2.5 bg-red-50 rounded-xl transition-colors duration-300">
                    <x-heroicon-o-banknotes class="w-6 h-6 text-red-600 transition-colors duration-300" />
                </div>
                <div class="w-20 h-8">
                    <svg viewBox="0 0 100 40" class="w-full h-full">
                        <path d="M0 35 Q 25 35, 50 25 T 100 5" fill="none" stroke="#EF4444" stroke-width="2" />
                        <path d="M0 35 Q 25 35, 50 25 T 100 5 V 40 H 0 Z" fill="url(#gradient-red)" opacity="0.1" />
                        <defs>
                            <linearGradient id="gradient-red" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#EF4444" />
                                <stop offset="100%" stop-color="#EF4444" stop-opacity="0" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-400">Pendapatan Bulan Ini</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-2xl font-bold text-gray-900">Rp 184 jt</h3>
                    <span class="flex items-center text-[11px] font-bold text-green-500 bg-green-50 px-1.5 py-0.5 rounded">
                        <x-heroicon-m-arrow-trending-up class="w-3 h-3 mr-0.5" />
                        12.8%
                    </span>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Booking --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-40 group hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="p-2.5 bg-blue-50 rounded-xl transition-colors duration-300">
                    <x-heroicon-o-calendar-days class="w-6 h-6 text-blue-600 transition-colors duration-300" />
                </div>
                <div class="w-20 h-8">
                    <svg viewBox="0 0 100 40" class="w-full h-full">
                        <path d="M0 30 Q 20 20, 40 35 T 80 15 T 100 20" fill="none" stroke="#3B82F6" stroke-width="2" />
                        <path d="M0 30 Q 20 20, 40 35 T 80 15 T 100 20 V 40 H 0 Z" fill="url(#gradient-blue)" opacity="0.1" />
                        <defs>
                            <linearGradient id="gradient-blue" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#3B82F6" />
                                <stop offset="100%" stop-color="#3B82F6" stop-opacity="0" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-400">Total Booking</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-2xl font-bold text-gray-900">342</h3>
                    <span class="flex items-center text-[11px] font-bold text-green-500 bg-green-50 px-1.5 py-0.5 rounded">
                        <x-heroicon-m-arrow-trending-up class="w-3 h-3 mr-0.5" />
                        8.4%
                    </span>
                </div>
            </div>
        </div>

        {{-- Card 3: Work Order Selesai --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-40 group hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="p-2.5 bg-green-50 rounded-xl transition-colors duration-300">
                    <x-heroicon-o-wrench-screwdriver class="w-6 h-6 text-green-600 transition-colors duration-300" />
                </div>
                <div class="w-20 h-8">
                    <svg viewBox="0 0 100 40" class="w-full h-full">
                        <path d="M0 35 L 20 25 L 40 30 L 60 15 L 80 20 L 100 5" fill="none" stroke="#10B981" stroke-width="2" />
                        <path d="M0 35 L 20 25 L 40 30 L 60 15 L 80 20 L 100 5 V 40 H 0 Z" fill="url(#gradient-green)" opacity="0.1" />
                        <defs>
                            <linearGradient id="gradient-green" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#10B981" />
                                <stop offset="100%" stop-color="#10B981" stop-opacity="0" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-400">Work Order Selesai</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-2xl font-bold text-gray-900">289</h3>
                    <span class="flex items-center text-[11px] font-bold text-green-500 bg-green-50 px-1.5 py-0.5 rounded">
                        <x-heroicon-m-arrow-trending-up class="w-3 h-3 mr-0.5" />
                        5.1%
                    </span>
                </div>
            </div>
        </div>

        {{-- Card 4: Pelanggan Baru --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-40 group hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="p-2.5 bg-purple-50 rounded-xl transition-colors duration-300">
                    <x-heroicon-o-users class="w-6 h-6 text-purple-600 transition-colors duration-300" />
                </div>
                <div class="w-20 h-8">
                    <svg viewBox="0 0 100 40" class="w-full h-full">
                        <path d="M0 5 Q 25 15, 50 10 T 100 35" fill="none" stroke="#8B5CF6" stroke-width="2" />
                        <path d="M0 5 Q 25 15, 50 10 T 100 35 V 40 H 0 Z" fill="url(#gradient-purple)" opacity="0.1" />
                        <defs>
                            <linearGradient id="gradient-purple" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#8B5CF6" />
                                <stop offset="100%" stop-color="#8B5CF6" stop-opacity="0" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-400">Pelanggan Baru</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-2xl font-bold text-gray-900">57</h3>
                    <span class="flex items-center text-[11px] font-bold text-red-500 bg-red-50 px-1.5 py-0.5 rounded">
                        <x-heroicon-m-arrow-trending-down class="w-3 h-3 mr-0.5" />
                        3.2%
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Revenue Chart --}}
        <div class="lg:col-span-2 bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Statistik Pendapatan</h3>
                    <p class="text-sm text-gray-400">Servis bengkel vs. penjualan online shop — 2026</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-600"></span>
                        <span class="text-xs font-semibold text-gray-500">Servis</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-indigo-400"></span>
                        <span class="text-xs font-semibold text-gray-500">Online Shop</span>
                    </div>
                </div>
            </div>

            <div class="h-72 w-full relative">
                {{-- Mock Chart with SVG --}}
                <svg viewBox="0 0 800 200" class="w-full h-full overflow-visible">
                    {{-- Grid Lines --}}
                    <line x1="0" y1="0" x2="800" y2="0" stroke="#F3F4F6" stroke-width="1" />
                    <line x1="0" y1="50" x2="800" y2="50" stroke="#F3F4F6" stroke-width="1" />
                    <line x1="0" y1="100" x2="800" y2="100" stroke="#F3F4F6" stroke-width="1" />
                    <line x1="0" y1="150" x2="800" y2="150" stroke="#F3F4F6" stroke-width="1" />
                    <line x1="0" y1="200" x2="800" y2="200" stroke="#F3F4F6" stroke-width="2" />

                    {{-- Online Shop Line (Blue) --}}
                    <path d="M0 160 Q 50 165, 100 150 T 200 155 T 300 140 T 400 145 T 500 130 T 600 135 T 700 125 T 800 120"
                        fill="none" stroke="#818CF8" stroke-width="3" stroke-linecap="round" />
                    <path d="M0 160 Q 50 165, 100 150 T 200 155 T 300 140 T 400 145 T 500 130 T 600 135 T 700 125 T 800 120 V 200 H 0 Z"
                        fill="url(#gradient-indigo)" opacity="0.1" />

                    {{-- Servis Line (Red) --}}
                    <path d="M0 130 Q 50 120, 100 115 T 200 100 T 300 105 T 400 80 T 500 90 T 600 60 T 700 70 T 800 40"
                        fill="none" stroke="#DC2626" stroke-width="4" stroke-linecap="round" />
                    <path d="M0 130 Q 50 120, 100 115 T 200 100 T 300 105 T 400 80 T 500 90 T 600 60 T 700 70 T 800 40 V 200 H 0 Z"
                        fill="url(#gradient-red-full)" opacity="0.15" />

                    <defs>
                        <linearGradient id="gradient-indigo" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#818CF8" />
                            <stop offset="100%" stop-color="#818CF8" stop-opacity="0" />
                        </linearGradient>
                        <linearGradient id="gradient-red-full" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#DC2626" />
                            <stop offset="100%" stop-color="#DC2626" stop-opacity="0" />
                        </linearGradient>
                    </defs>
                </svg>

                {{-- X-Axis Labels --}}
                <div class="flex justify-between mt-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest px-1">
                    <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>Mei</span><span>Jun</span><span>Jul</span><span>Agu</span><span>Sep</span><span>Okt</span><span>Nov</span><span>Des</span>
                </div>
            </div>
        </div>

        {{-- Work Order Status Chart --}}
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-bold text-gray-900">Status Work Order</h3>
                <button class="text-gray-400 hover:text-gray-600">
                    <x-heroicon-o-ellipsis-vertical class="w-6 h-6" />
                </button>
            </div>

            <div class="flex-1 flex flex-col items-center justify-center relative">
                <div class="relative w-52 h-52">
                    <svg viewBox="0 0 36 36" class="w-full h-full transform -rotate-90">
                        <circle cx="18" cy="18" r="16" fill="none" stroke="#F3F4F6" stroke-width="4" />
                        {{-- Selesai (Green) - 70% --}}
                        <circle cx="18" cy="18" r="16" fill="none" stroke="#10B981" stroke-width="4" stroke-dasharray="70 100" />
                        {{-- Dikerjakan (Blue) - 15% --}}
                        <circle cx="18" cy="18" r="16" fill="none" stroke="#3B82F6" stroke-width="4" stroke-dasharray="15 100" stroke-dashoffset="-70" />
                        {{-- Menunggu (Yellow) - 10% --}}
                        <circle cx="18" cy="18" r="16" fill="none" stroke="#F59E0B" stroke-width="4" stroke-dasharray="10 100" stroke-dashoffset="-85" />
                        {{-- Dibatalkan (Red) - 5% --}}
                        <circle cx="18" cy="18" r="16" fill="none" stroke="#EF4444" stroke-width="4" stroke-dasharray="5 100" stroke-dashoffset="-95" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                        <span class="text-3xl font-black text-gray-900 leading-none">349</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Total WO</span>
                    </div>
                </div>

                <div class="w-full mt-10 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                            <span class="text-sm font-semibold text-gray-500">Selesai</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">289</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <span class="text-sm font-semibold text-gray-500">Dikerjakan</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">34</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span class="text-sm font-semibold text-gray-500">Menunggu</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">19</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                            <span class="text-sm font-semibold text-gray-500">Dibatalkan</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">7</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Table --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-white">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Booking Terbaru</h3>
                <p class="text-sm text-gray-400">Jadwal servis masuk hari ini & kemarin</p>
            </div>
            <button class="px-5 py-2.5 bg-gray-50 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-100 transition-colors">
                Lihat semua
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.15em] bg-gray-50/50">
                        <th class="px-8 py-5">No. Booking</th>
                        <th class="px-6 py-5">Pelanggan</th>
                        <th class="px-6 py-5">Kendaraan</th>
                        <th class="px-6 py-5">Layanan</th>
                        <th class="px-6 py-5">Jadwal</th>
                        <th class="px-6 py-5">Mekanik</th>
                        <th class="px-8 py-5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr class="group hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-5">
                            <span class="text-sm font-bold text-red-600">BK-2026-0342</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center text-[11px] font-bold text-red-600">
                                    AP
                                </div>
                                <span class="text-sm font-bold text-gray-700">Andi Pratama</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2 text-gray-500">
                                <x-heroicon-o-wrench-screwdriver class="w-4 h-4 opacity-40" />
                                <span class="text-sm font-medium">Honda Vario 160</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-sm font-medium text-gray-600">Servis Rutin + Ganti Oli</span>
                        </td>
                        <td class="px-6 py-5 text-sm">
                            <p class="font-bold text-gray-700">30 Mei 2026</p>
                            <p class="text-[11px] font-medium text-gray-400">09:00 WIB</p>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-sm font-bold text-gray-700">Rizky M.</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                <span class="text-sm font-bold text-gray-900">Dikerjakan</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="group hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-5">
                            <span class="text-sm font-bold text-red-600">BK-2026-0341</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-[11px] font-bold text-blue-600">
                                    SN
                                </div>
                                <span class="text-sm font-bold text-gray-700">Siti Nurhaliza</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2 text-gray-500">
                                <x-heroicon-o-wrench-screwdriver class="w-4 h-4 opacity-40" />
                                <span class="text-sm font-medium">Yamaha NMAX</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-sm font-medium text-gray-600">Ganti Kampas Rem</span>
                        </td>
                        <td class="px-6 py-5 text-sm">
                            <p class="font-bold text-gray-700">30 Mei 2026</p>
                            <p class="text-[11px] font-medium text-gray-400">10:30 WIB</p>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-sm font-bold text-gray-700">Fajar N.</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span class="text-sm font-bold text-gray-900">Menunggu</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- RFM Segmentation --}}
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Segmentasi RFM</h3>
                <p class="text-sm text-gray-400">Klaster pelanggan berbasis AI</p>
            </div>
            <span class="px-3 py-1 bg-red-50 text-red-600 text-[11px] font-bold rounded-full uppercase tracking-wider">
                445 Pelanggan
            </span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-8">
            {{-- Champions --}}
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-bold text-gray-900">Champions</span>
                    <span class="text-sm font-bold text-gray-900">128</span>
                </div>
                <div class="h-2 bg-gray-50 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 rounded-full" style="width: 100%"></div>
                </div>
                <p class="text-[11px] text-gray-400 mt-2 font-medium">Sering & baru servis</p>
            </div>
            {{-- Loyal --}}
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-bold text-gray-900">Loyal</span>
                    <span class="text-sm font-bold text-gray-900">96</span>
                </div>
                <div class="h-2 bg-gray-50 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-600 rounded-full" style="width: 75%"></div>
                </div>
                <p class="text-[11px] text-gray-400 mt-2 font-medium">Pelanggan setia</p>
            </div>
            {{-- Potential --}}
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-bold text-gray-900">Potential</span>
                    <span class="text-sm font-bold text-gray-900">74</span>
                </div>
                <div class="h-2 bg-gray-50 rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-500 rounded-full" style="width: 58%"></div>
                </div>
                <p class="text-[11px] text-gray-400 mt-2 font-medium">Berpotensi loyal</p>
            </div>
            {{-- At Risk --}}
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-bold text-gray-900">At Risk</span>
                    <span class="text-sm font-bold text-gray-900">52</span>
                </div>
                <div class="h-2 bg-gray-50 rounded-full overflow-hidden">
                    <div class="h-full bg-amber-500 rounded-full" style="width: 40%"></div>
                </div>
                <p class="text-[11px] text-gray-400 mt-2 font-medium">Lama tak kembali</p>
            </div>
            {{-- Hibernating --}}
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-bold text-gray-900">Hibernating</span>
                    <span class="text-sm font-bold text-gray-900">38</span>
                </div>
                <div class="h-2 bg-gray-50 rounded-full overflow-hidden">
                    <div class="h-full bg-red-500 rounded-full" style="width: 30%"></div>
                </div>
                <p class="text-[11px] text-gray-400 mt-2 font-medium">Hampir hilang</p>
            </div>
        </div>
    </div>

    {{-- Low Stock Table --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-white">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Stok Sparepart Menipis</h3>
                <p class="text-sm text-gray-400">Perlu segera di-restock</p>
            </div>
            <button class="px-5 py-2.5 bg-gray-50 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-100 transition-colors flex items-center gap-2">
                <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                Export
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.15em] bg-gray-50/50">
                        <th class="px-8 py-5">SKU</th>
                        <th class="px-6 py-5">Nama Item</th>
                        <th class="px-6 py-5">Kategori</th>
                        <th class="px-6 py-5">Merek</th>
                        <th class="px-6 py-5">Stok</th>
                        <th class="px-6 py-5 text-right">Harga Jual</th>
                        <th class="px-8 py-5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr class="group hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-5">
                            <span class="text-xs font-mono text-gray-500">OIL-MPX2-08</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400">
                                    <x-heroicon-o-cube class="w-5 h-5" />
                                </div>
                                <span class="text-sm font-bold text-gray-900">Oli Mesin AHM MPX2 0.8L</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-sm text-gray-600">Pelumas</td>
                        <td class="px-6 py-5 text-sm text-gray-600">AHM</td>
                        <td class="px-6 py-5">
                            <span class="text-sm font-black text-amber-500">6 <span class="text-[11px] font-bold text-gray-400">/ 20</span></span>
                        </td>
                        <td class="px-6 py-5 text-sm font-bold text-gray-900 text-right">Rp 48.000</td>
                        <td class="px-8 py-5">
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold rounded-full uppercase tracking-wider">Stok Menipis</span>
                        </td>
                    </tr>
                    <tr class="group hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-5">
                            <span class="text-xs font-mono text-gray-500">BRK-VAR-FR</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400">
                                    <x-heroicon-o-cube class="w-5 h-5" />
                                </div>
                                <span class="text-sm font-bold text-gray-900">Kampas Rem Depan Vario</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-sm text-gray-600">Pengereman</td>
                        <td class="px-6 py-5 text-sm text-gray-600">Honda</td>
                        <td class="px-6 py-5">
                            <span class="text-sm font-black text-amber-500">3 <span class="text-[11px] font-bold text-gray-400">/ 15</span></span>
                        </td>
                        <td class="px-6 py-5 text-sm font-bold text-gray-900 text-right">Rp 85.000</td>
                        <td class="px-8 py-5">
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold rounded-full uppercase tracking-wider">Stok Menipis</span>
                        </td>
                    </tr>
                    <tr class="group hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-5">
                            <span class="text-xs font-mono text-gray-500">SPK-NGK-CPR8</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400">
                                    <x-heroicon-o-cube class="w-5 h-5" />
                                </div>
                                <span class="text-sm font-bold text-gray-900">Busi NGK CPR8EA-9</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-sm text-gray-600">Kelistrikan</td>
                        <td class="px-6 py-5 text-sm text-gray-600">NGK</td>
                        <td class="px-6 py-5">
                            <span class="text-sm font-black text-red-500">0 <span class="text-[11px] font-bold text-gray-400">/ 30</span></span>
                        </td>
                        <td class="px-6 py-5 text-sm font-bold text-gray-900 text-right">Rp 32.000</td>
                        <td class="px-8 py-5">
                            <span class="px-2.5 py-1 bg-red-50 text-red-600 text-[10px] font-bold rounded-full uppercase tracking-wider">Habis</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
