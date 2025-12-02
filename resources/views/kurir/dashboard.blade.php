<x-layouts.app title="Dashboard Kurir">
    <div class="px-3 pt-3">
        
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-l1 font-bold">Dashboard Kurir</h1>
            <p class="text-neutral-300">Halo, {{ Auth::user()->display_name }}!</p>
        </div>

        {{-- Statistik Hari Ini --}}
        <div class="mb-6">
            <h2 class="text-l2 font-semibold mb-3">Pengantaran Hari Ini</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                {{-- Ditugaskan --}}
                <div class="bg-white shadow-md rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-neutral-300 text-1">Ditugaskan</p>
                            <p class="text-h2 font-bold text-primary-200">{{ $ditugaskan }}</p>
                        </div>
                        <i class="ph-fill ph-clipboard-text text-4xl text-primary-200"></i>
                    </div>
                </div>

                {{-- Dikirim --}}
                <div class="bg-white shadow-md rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-neutral-300 text-1">Sedang Dikirim</p>
                            <p class="text-h2 font-bold text-neutral-200">{{ $dikirim }}</p>
                        </div>
                        <i class="ph-fill ph-truck text-4xl text-neutral-200"></i>
                    </div>
                </div>

                {{-- Selesai --}}
                <div class="bg-white shadow-md rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-neutral-300 text-1">Selesai</p>
                            <p class="text-h2 font-bold text-green-500">{{ $selesai }}</p>
                        </div>
                        <i class="ph-fill ph-check-circle text-4xl text-green-500"></i>
                    </div>
                </div>

                {{-- Dibatalkan --}}
                <div class="bg-white shadow-md rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-neutral-300 text-1">Dibatalkan</p>
                            <p class="text-h2 font-bold text-secondary">{{ $dibatalkan }}</p>
                        </div>
                        <i class="ph-fill ph-x-circle text-4xl text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Statistik --}}
        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="bg-gradient-to-br from-primary to-primary-200 text-white shadow-md rounded-lg p-6">
                <h3 class="text-reguler font-medium mb-2">Total Pengantaran</h3>
                <p class="text-h1 font-bold">{{ $totalDeliveries }}</p>
                <p class="text-1 mt-1">Sepanjang waktu</p>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white shadow-md rounded-lg p-6">
                <h3 class="text-reguler font-medium mb-2">Total Berhasil</h3>
                <p class="text-h1 font-bold">{{ $totalSelesai }}</p>
                <p class="text-1 mt-1">Pengantaran selesai</p>
            </div>
        </div>

        {{-- Pengantaran Aktif --}}
        <div class="mb-6">
            <h2 class="text-l2 font-semibold mb-3">Pengantaran Aktif</h2>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                @if($activeDeliveries->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">No</th>
                                <th class="px-4 py-3 text-left">Outlet Tujuan</th>
                                <th class="px-4 py-3 text-left">Ditugaskan</th>
                                <th class="px-4 py-3 text-left">Waktu Kirim</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                                <th class="px-4 py-3 text-center">Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeDeliveries as $delivery)
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">{{ $delivery->outlet->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    {{ \Carbon\Carbon::parse($delivery->assigned_at, 'Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                </td>
                                <td class="px-4 py-3">
                                    {{ $delivery->delivered_at ? \Carbon\Carbon::parse($delivery->delivered_at, 'Asia/Jakarta')->format('d M Y, H:i') . ' WIB' : '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span x-data :class="{
                                        'border border-primary-200 text-primary-200': '{{ $delivery->status }}' === 'DITUGASKAN',
                                        'border border-neutral-200 text-neutral-200': '{{ $delivery->status }}' === 'DIKIRIM',
                                    }" class="py-1 px-3 rounded-xl block text-center text-xs lg:text-1">
                                        {{ $delivery->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Tombol Mulai Kirim / Sedang Dikirim --}}
                                        @if($delivery->status === 'DITUGASKAN')
                                            <form action="{{ route('delivery.start', $delivery->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                                                    Mulai Kirim
                                                </button>
                                            </form>
                                        @elseif($delivery->status === 'DIKIRIM')
                                            <button disabled class="px-3 py-1 text-sm font-medium text-gray-500 bg-gray-200 rounded-lg cursor-not-allowed border border-gray-300">
                                                Sedang Dikirim
                                            </button>
                                        @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                        {{-- Tombol Detail --}}
                                        <div x-data="{ open: false }">
                                            <button @click="open = true" class="px-3 py-1 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-200 transition-colors">
                                                Detail
                                            </button>

                                        {{-- Modal Backdrop --}}
                                        <div x-show="open" 
                                             x-transition.opacity
                                             class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm"
                                             @click="open = false"></div>

                                        {{-- Modal Content --}}
                                        <div x-show="open"
                                             x-transition:enter="transition ease-out duration-300"
                                             x-transition:enter-start="opacity-0 scale-90"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-200"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-90"
                                             class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none">
                                            
                                            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto pointer-events-auto" @click.stop>
                                                {{-- Modal Header --}}
                                                <div class="flex items-center justify-between p-4 border-b border-gray-100">
                                                    <h3 class="text-lg font-bold text-gray-900">Detail Pengantaran</h3>
                                                    <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                                                        <i class="ph ph-x text-xl"></i>
                                                    </button>
                                                </div>

                                                {{-- Modal Body --}}
                                                <div class="p-4 space-y-4 text-left">
                                                    {{-- Info Utama --}}
                                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                                        <div>
                                                            <p class="text-gray-500">Outlet Tujuan</p>
                                                            <p class="font-semibold">{{ $delivery->outlet->name ?? '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-gray-500">Alamat Outlet</p>
                                                            <p class="font-semibold">{{ $delivery->outlet->location ?? '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-gray-500">Status</p>
                                                            <p class="font-semibold text-primary">{{ $delivery->status }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-gray-500">Ditugaskan Oleh</p>
                                                            <p class="font-semibold">{{ $delivery->inventaris->display_name ?? '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-gray-500">Waktu Tugas</p>
                                                            <p class="font-semibold">{{ \Carbon\Carbon::parse($delivery->assigned_at)->format('d M Y, H:i') }}</p>
                                                        </div>
                                                    </div>

                                                    {{-- Daftar Barang --}}
                                                    <div>
                                                        <h4 class="font-bold text-gray-900 mb-2">Daftar Barang</h4>
                                                        <div class="bg-gray-50 rounded-xl p-3">
                                                            <ul class="space-y-2">
                                                                @foreach($delivery->items as $item)
                                                                <li class="flex items-center justify-between text-sm">
                                                                    <span class="text-gray-700">{{ $item->item->name ?? 'Item dihapus' }}</span>
                                                                    <span class="font-semibold text-gray-900">x{{ $item->quantity }}</span>
                                                                </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    {{-- Bukti Foto (jika ada) --}}
                                                    @if($delivery->photo_evidence)
                                                    <div>
                                                        <h4 class="font-bold text-gray-900 mb-2">Bukti Foto</h4>
                                                        <img src="{{ $delivery->photo_evidence }}" alt="Bukti Pengantaran" class="w-full h-48 object-cover rounded-lg">
                                                    </div>
                                                    @endif
                                                </div>

                                                {{-- Modal Footer --}}
                                                <div class="p-4 border-t border-gray-100 flex justify-end">
                                                    <button @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                                        Tutup
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-8 text-center text-neutral-300">
                    <i class="ph-fill ph-package text-6xl mb-3"></i>
                    <p>Tidak ada pengantaran aktif saat ini</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Pengantaran Hari Ini Detail --}}
        <div class="mb-6">
            <h2 class="text-l2 font-semibold mb-3">Pengantaran Hari Ini - Detail</h2>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                @if($todayDeliveries->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">No</th>
                                <th class="px-4 py-3 text-left">Outlet</th>
                                <th class="px-4 py-3 text-left">Inventaris</th>
                                <th class="px-4 py-3 text-left">Waktu Ditugaskan</th>
                                <th class="px-4 py-3 text-left">Waktu Kirim</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayDeliveries as $delivery)
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">{{ $delivery->outlet->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $delivery->inventaris->display_name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    {{ \Carbon\Carbon::parse($delivery->assigned_at, 'Asia/Jakarta')->format('H:i') }} WIB
                                </td>
                                <td class="px-4 py-3">
                                    {{ $delivery->delivered_at ? \Carbon\Carbon::parse($delivery->delivered_at, 'Asia/Jakarta')->format('H:i') . ' WIB' : '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span x-data :class="{
                                        'border border-green-500 text-green-500': '{{ $delivery->status }}' === 'SELESAI',
                                        'border border-primary-200 text-primary-200': '{{ $delivery->status }}' === 'DITUGASKAN',
                                        'border border-neutral-200 text-neutral-200': '{{ $delivery->status }}' === 'DIKIRIM',
                                        'border border-secondary text-secondary': '{{ $delivery->status }}' === 'DIBATALKAN',
                                    }" class="py-1 px-3 rounded-xl block text-center text-xs lg:text-1">
                                        {{ $delivery->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div x-data="{ open: false }">
                                        <button @click="open = true" class="px-3 py-1 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-200 transition-colors">
                                            Detail
                                        </button>

                                        {{-- Modal Backdrop --}}
                                        <div x-show="open" 
                                             x-transition.opacity
                                             class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm"
                                             @click="open = false"></div>

                                        {{-- Modal Content --}}
                                        <div x-show="open"
                                             x-transition:enter="transition ease-out duration-300"
                                             x-transition:enter-start="opacity-0 scale-90"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-200"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-90"
                                             class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none">
                                            
                                            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto pointer-events-auto" @click.stop>
                                                {{-- Modal Header --}}
                                                <div class="flex items-center justify-between p-4 border-b border-gray-100">
                                                    <h3 class="text-lg font-bold text-gray-900">Detail Pengantaran</h3>
                                                    <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                                                        <i class="ph ph-x text-xl"></i>
                                                    </button>
                                                </div>

                                                {{-- Modal Body --}}
                                                <div class="p-4 space-y-4 text-left">
                                                    {{-- Info Utama --}}
                                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                                        <div>
                                                            <p class="text-gray-500">Outlet Tujuan</p>
                                                            <p class="font-semibold">{{ $delivery->outlet->name ?? '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-gray-500">Alamat Outlet</p>
                                                            <p class="font-semibold">{{ $delivery->outlet->location ?? '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-gray-500">Status</p>
                                                            <p class="font-semibold text-primary">{{ $delivery->status }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-gray-500">Ditugaskan Oleh</p>
                                                            <p class="font-semibold">{{ $delivery->inventaris->display_name ?? '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-gray-500">Waktu Tugas</p>
                                                            <p class="font-semibold">{{ \Carbon\Carbon::parse($delivery->assigned_at)->format('d M Y, H:i') }}</p>
                                                        </div>
                                                    </div>

                                                    {{-- Daftar Barang --}}
                                                    <div>
                                                        <h4 class="font-bold text-gray-900 mb-2">Daftar Barang</h4>
                                                        <div class="bg-gray-50 rounded-xl p-3">
                                                            <ul class="space-y-2">
                                                                @foreach($delivery->items as $item)
                                                                <li class="flex items-center justify-between text-sm">
                                                                    <span class="text-gray-700">{{ $item->item->name ?? 'Item dihapus' }}</span>
                                                                    <span class="font-semibold text-gray-900">x{{ $item->quantity }}</span>
                                                                </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    {{-- Bukti Foto (jika ada) --}}
                                                    @if($delivery->photo_evidence)
                                                    <div>
                                                        <h4 class="font-bold text-gray-900 mb-2">Bukti Foto</h4>
                                                        <img src="{{ $delivery->photo_evidence }}" alt="Bukti Pengantaran" class="w-full h-48 object-cover rounded-lg">
                                                    </div>
                                                    @endif
                                                </div>

                                                {{-- Modal Footer --}}
                                                <div class="p-4 border-t border-gray-100 flex justify-end">
                                                    <button @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                                        Tutup
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-8 text-center text-neutral-300">
                    <i class="ph-fill ph-calendar-x text-6xl mb-3"></i>
                    <p>Belum ada pengantaran hari ini</p>
                </div>
                @endif
            </div>
        </div>

    </div>
</x-layouts.app>
