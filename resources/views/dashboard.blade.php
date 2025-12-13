<x-layouts.app title="Dashboard">
    <div class="md:justify-center mx-auto ">
        {{-- Grafik --}}
        <div class="mt-12 w-full ">
            <h1 class="border-b-2 border-b-primary w-fit font-medium lg:text-l1">Status</h1>
            <div class="mt-8 lg:w-full grid grid-cols-12 gap-4">
                <a href="/pengantaran" class="col-span-12 md:col-span-4 text-center  shadow-reguler rounded-lg p-4">
                    <h1 class="font-medium">Status pengantaran hari ini</h1>
                    <livewire:dashboard-pengantaran-chart />
                </a>
                <a href="/absensi"
                    class="col-span-12 mt-4 md:mt-0 md:col-span-4 text-center  shadow-reguler rounded-lg p-4">
                    <h1 class="font-medium">Status absensi hari ini</h1>
                    <livewire:dashboard-absensi-chart />
                </a>
            </div>
        </div>
        {{-- End of Grafik --}}

        {{-- Outlet --}}
        <div class="w-full pt-8 ">
            <h1 class="border-b-2 border-b-primary w-fit font-medium text-l2 lg:text-l1">Outlet</h1>
            <div class="mt-4 grid grid-cols-12 gap-2">
                @foreach ($outlets as $outlet)
                    <div
                        class="p-6 mt-2 shadow-reguler gap-5 rounded-lg col-span-12 md:col-span-6 lg:col-span-4 flex flex-col justify-between">
                        <div class="">
                            <div class="flex items-center justify-between">
                                <h1>Status: {{ $outlet->status }}</h1>
                                <img src="{{ asset($outlet->status === 'AKTIF' ? 'green-dot.png' : 'red-dot.png') }}"
                                    class="w-[40px]" alt="">
                            </div>
                            <h2 class="text-reguler font-medium">{{ $outlet->name }}</h2>
                            <p class="text-1 text-neutral-300">{{ $outlet->location }}</p>
                        </div>
                        <span x-data="{ modalIsOpen: false }">
                            <button x-on:click="modalIsOpen = true" type="button"
                                class="flex items-center mt-2 shadow-button w-fit px-4 py-2 rounded-lg cursor-pointer gap-2">
                                <i class="ph ph-files"></i> Detail</button>
                            <div x-cloak x-show="modalIsOpen" x-transition.opacity.duration.200ms
                                x-trap.inert.noscroll="modalIsOpen" x-on:keydown.esc.window="modalIsOpen = false"
                                x-on:click.self="modalIsOpen = false"
                                class="fixed inset-0 z-30 flex items-center justify-center p-4 pb-8 lg:p-8 bg-neutral-500/30 backdrop-blur-xs"
                                x-transition.opacity role="dialog" aria-modal="true"
                                aria-labelledby="defaultModalTitle">
                                <!-- Modal Dialog -->
                                <div x-show="modalIsOpen"
                                    @click.stop
                                    x-transition:enter="transition ease-out duration-200 delay-100 motion-reduce:transition-opacity"
                                    x-transition:enter-start="opacity-0 translate-y-8"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    class="flex flex-col gap-4 overflow-x-hidden overflow-y-scroll h-[400px] xl:h-[500px] rounded-radius border border-outline bg-white w-[90%] xl:max-w-[900px] md:mt-16 lg:mt-24 lg:ml-30">
                                    <!-- Dialog Header -->
                                    <div class="flex items-center justify-between border-b border-outline p-4">
                                        <h3 id="defaultModalTitle" class="font-bold tracking-wide text-l2">
                                            Detail Outlet</h3>
                                        <button x-on:click="modalIsOpen = false" aria-label="close modal">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                aria-hidden="true" stroke="currentColor" fill="none"
                                                stroke-width="1.4" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <!-- Dialog Body -->
                                    <div class="px-4 pt-8 lg:text-1 grid grid-cols-12 items-center">
                                        <div class="col-span-12 lg:col-span-4 px-4 text-sm md:text-reguler space-y-2">
                                            <div class="">
                                                <h1 class="text-center font-bold">Profil outlet</h1>
                                                <div class="mt-2">
                                                    <p><span class="font-medium">Nama outlet:</span>
                                                        {{ $outlet->name }}</p>
                                                    <p><span class="font-medium">Lokasi:</span>
                                                        {{ $outlet->location }}
                                                    </p>
                                                    <div class="flex items-center justify-between gap-2 md:gap-0">
                                                        <p><span class="font-medium">Status:</span>
                                                            {{ $outlet->status }}
                                                        </p>
                                                        <img src="{{ asset($outlet->status === 'AKTIF' ? 'green-dot.png' : 'red-dot.png') }}"
                                                            class="w-[20px] md:w-[40px]" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="mt-6 col-span-12 lg:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="p-4 bg-white border border-outline rounded-xl shadow-sm">
                                                <h4 class="text-sm font-bold text-gray-700 mb-2 text-center">Komposisi
                                                    Stok Barang</h4>
                                                <div class="relative h-48 w-full">
                                                    <canvas id="chartStock-{{ $outlet->id }}"></canvas>
                                                </div>
                                            </div>

                                            <div class="p-4 bg-white border border-outline rounded-xl shadow-sm">
                                                <h4 class="text-sm font-bold text-gray-700 mb-2 text-center">Kehadiran
                                                    Staff Hari Ini</h4>
                                                <div class="relative h-48 w-full">
                                                    <canvas id="chartAttendance-{{ $outlet->id }}"></canvas>
                                                </div>
                                            </div>

                                        </div>

                                        @php
                                            // PENTING: Bungkus dengan collect( ... ?? [] ) agar tidak error jika data null
                                            $itemSettings = collect($outlet->hasItemSetting ?? []);
                                            $staffList = collect($outlet->hasStaff ?? []);

                                            // 1. Data Stok (Menggunakan $itemSettings yang sudah aman)
                                            $stockBahan = $itemSettings
                                                ->where('type', 'BAHAN_MENTAH')
                                                ->sum('pivot.quantity');
                                            $stockKemasan = $itemSettings
                                                ->where('type', 'KEMASAN')
                                                ->sum('pivot.quantity');
                                            $stockPenunjang = $itemSettings
                                                ->where('type', 'BAHAN_PENUNJANG')
                                                ->sum('pivot.quantity');

                                            // 2. Data Kehadiran (Menggunakan $staffList yang sudah aman)
                                            $staffHadir = $staffList
                                                ->filter(fn($s) => optional($s->todayAttendance)->status == 'HADIR')
                                                ->count();
                                            $staffIzin = $staffList
                                                ->filter(fn($s) => optional($s->todayAttendance)->status == 'IZIN')
                                                ->count();
                                            $staffSakit = $staffList
                                                ->filter(fn($s) => optional($s->todayAttendance)->status == 'SAKIT')
                                                ->count();

                                            // Status Absen (Termasuk yang datanya null)
                                            $staffAbsen = $staffList
                                                ->filter(
                                                    fn($s) => in_array(optional($s->todayAttendance)->status, [
                                                        'ABSEN',
                                                        null,
                                                    ]),
                                                )
                                                ->count();
                                        @endphp
                                        <script>
                                            // Kita jalankan fungsi segera (IIFE) atau saat event load agar variabel aman
                                            document.addEventListener("DOMContentLoaded", function() {

                                                // --- Chart Stok (Unik per Outlet ID) ---
                                                const ctxStock{{ $outlet->id }} = document.getElementById('chartStock-{{ $outlet->id }}')
                                                    .getContext('2d');

                                                // Cek jika chart instance sudah ada agar tidak numpuk (optional safety)
                                                if (Chart.getChart("chartStock-{{ $outlet->id }}")) {
                                                    Chart.getChart("chartStock-{{ $outlet->id }}").destroy();
                                                }

                                                new Chart(ctxStock{{ $outlet->id }}, {
                                                    type: 'doughnut',
                                                    data: {
                                                        labels: ['Bahan Mentah', 'Kemasan', 'Penunjang'],
                                                        datasets: [{
                                                            data: [{{ $stockBahan }}, {{ $stockKemasan }}, {{ $stockPenunjang }}],
                                                            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b'],
                                                            borderWidth: 0
                                                        }]
                                                    },
                                                    options: {
                                                        responsive: true,
                                                        maintainAspectRatio: false,
                                                        plugins: {
                                                            legend: {
                                                                position: 'right',
                                                                labels: {
                                                                    boxWidth: 12,
                                                                    font: {
                                                                        size: 10
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                });

                                                // --- Chart Kehadiran (Unik per Outlet ID) ---
                                                const ctxAtt{{ $outlet->id }} = document.getElementById('chartAttendance-{{ $outlet->id }}')
                                                    .getContext('2d');

                                                if (Chart.getChart("chartAttendance-{{ $outlet->id }}")) {
                                                    Chart.getChart("chartAttendance-{{ $outlet->id }}").destroy();
                                                }

                                                new Chart(ctxAtt{{ $outlet->id }}, {
                                                    type: 'bar',
                                                    data: {
                                                        labels: ['Hadir', 'Izin', 'Sakit', 'Absen'],
                                                        datasets: [{
                                                            label: 'Jumlah Staff',
                                                            data: [{{ $staffHadir }}, {{ $staffIzin }}, {{ $staffSakit }},
                                                                {{ $staffAbsen }}
                                                            ],
                                                            backgroundColor: ['#22c55e', '#eab308', '#ef4444', '#9ca3af'],
                                                            borderRadius: 4,
                                                            barThickness: 20
                                                        }]
                                                    },
                                                    options: {
                                                        indexAxis: 'y',
                                                        responsive: true,
                                                        maintainAspectRatio: false,
                                                        plugins: {
                                                            legend: {
                                                                display: false
                                                            }
                                                        },
                                                        scales: {
                                                            x: {
                                                                beginAtZero: true,
                                                                grid: {
                                                                    display: false
                                                                }
                                                            },
                                                            y: {
                                                                grid: {
                                                                    display: false
                                                                }
                                                            }
                                                        }
                                                    }
                                                });
                                            });
                                        </script>



                                    </div>
                                    <div class="px-4">
                                        <div class="px-4 py-4 mt-4 shadow-reguler">
                                            <h1 class="font-bold text-center text-l1">Absensi Staff</h1>
                                            <div class="mt-4 overflow-x-auto w-full">
                                                <table class="min-w-max w-full text-sm">
                                                    <thead>
                                                        <tr>
                                                            <th class="px-4 py-3 text-left">No</th>
                                                            <th class="px-4 py-3 text-left">Nama</th>
                                                            <th class="px-4 py-3 text-left">Tanggal</th>
                                                            <th class="px-4 py-3 text-left">Waktu</th>
                                                            <th class="px-4 py-3 text-left">Status</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @forelse ($outlet->hasStaff ?? [] as $staff)
                                                            <tr class="">
                                                                <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                                                <td class="px-4 py-3">
                                                                    {{ $staff->display_name ?? '-' }}
                                                                </td>

                                                                <td class="px-4 py-3">
                                                                    {{ \Carbon\Carbon::parse($staff->todayAttendance->attendance_date, 'Asia/Jakarta')->format('d F Y') }}
                                                                </td>

                                                                <td class="px-4 py-3">
                                                                    {{ $staff->todayAttendance->attendance_time }} WIB
                                                                </td>
                                                                <td class="px-4 py-3">
                                                                    <span x-data
                                                                        :class="{
                                                                            'border border-green-500 text-green-500': '{{ $staff->todayAttendance->status }}'
                                                                            === 'HADIR',
                                                                            'border border-primary-200 text-primary-200': '{{ $staff->todayAttendance->status }}'
                                                                            === 'IZIN',
                                                                            'border border-secondary text-secondary': '{{ $staff->todayAttendance->status }}'
                                                                            === 'SAKIT',
                                                                            'border border-neutral-200 text-neutral-200':
                                                                                ![
                                                                                    'HADIR', 'IZIN', 'SAKIT'
                                                                                ].includes(
                                                                                    '{{ $staff->todayAttendance->status }}'
                                                                                ),
                                                                        }"
                                                                        class="p-2 px-3 rounded-xl block text-center text-xs lg:text-1 w-[100px]">
                                                                        {{ $staff->todayAttendance->status }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6"
                                                                    class="text-center py-4 text-gray-500">
                                                                    Tidak ada data</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="px-4 py-4 mt-8 shadow-reguler">
                                            <h1 class="font-bold text-center text-l1">Pengantaran</h1>
                                            <div class="mt-4 overflow-x-auto w-full">
                                                <table class="min-w-max w-full text-sm">
                                                    <thead>
                                                        <tr>
                                                            <th class="px-4 py-3 font-semibold text-left w-12">No</th>
                                                            <th class="px-4 py-3 font-semibold text-left">Nama Kurir
                                                            </th>
                                                            <th class="px-4 py-3 font-semibold text-left">Waktu
                                                                Ditugaskan
                                                            </th>
                                                            <th class="px-4 py-3 font-semibold text-left">Waktu Kirim
                                                            </th>
                                                            <th class="px-4 py-3 font-semibold text-center">Status</th>
                                                            <th class="px-4 py-3 font-semibold text-center">Konfirmasi
                                                            </th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @forelse ($outlet->delivery as $delivery)
                                                            <tr class="border-b border-gray-100">
                                                                <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                                                <td class="px-4 py-3">
                                                                    {{ $delivery->kurir->display_name ?? '-' }}
                                                                </td>

                                                                <td class="px-4 py-3">
                                                                    {{ \Carbon\Carbon::parse($delivery->assigned_at, 'Asia/Jakarta')->format('d F Y, H:i') }}
                                                                    WIB
                                                                </td>

                                                                <td class="px-4 py-3">
                                                                    {{ \Carbon\Carbon::parse($delivery->delivered_at, 'Asia/Jakarta')->format('d F Y, H:i') }}
                                                                    WIB
                                                                </td>
                                                                <td class="px-4 py-3">
                                                                    <p x-data
                                                                        :class="{
                                                                            'border border-secondary text-secondary': '{{ $delivery->status }}'
                                                                            === 'DIBATALKAN',
                                                                            'border border-primary-200 text-primary-200': '{{ $delivery->status }}'
                                                                            === 'DITUGASKAN',
                                                                            'border border-green-500 text-green-500': '{{ $delivery->status }}'
                                                                            === 'SELESAI',
                                                                            'border border-neutral-200 text-neutral-200': '{{ $delivery->status }}'
                                                                            === 'DIKIRIM',
                                                                        }"
                                                                        class="p-2 px-3 rounded-xl text-center text-xs lg:text-1">
                                                                        {{ $delivery->status }}
                                                                    </p>
                                                                </td>
                                                                <td class="px-4 py-3">
                                                                    {{ optional($delivery->hasDeliveryConfirmation)->received_at
                                                                        ? \Carbon\Carbon::parse($delivery->hasDeliveryConfirmation->received_at, 'Asia/Jakarta')->format('d F Y H:i') .
                                                                            ' WIB'
                                                                        : 'Belum dikonfirmasi' }}
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6"
                                                                    class="text-center py-4 text-gray-500">
                                                                    Tidak ada data</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="px-4 py-4 mt-8 shadow-reguler">
                                            <h1 class="font-bold text-center text-l1">Kesalahan Pengantaran</h1>
                                            <div class="mt-4 overflow-x-auto w-full">
                                                <table class="min-w-max w-full text-sm">
                                                    <thead>
                                                        <tr>
                                                            <th class="px-4 py-3 font-semibold text-left w-12">No</th>
                                                            <th class="px-4 py-3 font-semibold text-left">Nama Kurir
                                                            </th>
                                                            <th class="px-4 py-3 font-semibold text-left">Staff yang
                                                                menerima
                                                            </th>
                                                            <th class="px-4 py-3 font-semibold text-center">Foto</th>
                                                            <th class="px-4 py-3 font-semibold text-center">Catatan
                                                            </th>
                                                            <th class="px-4 py-3 font-semibold text-center">Waktu
                                                                pelaporan
                                                            </th>
                                                            <th class="px-4 py-3 font-semibold text-center">Waktu
                                                                Konfirmasi
                                                            </th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @php
                                                            $mistakes = $outlet->delivery->filter(
                                                                fn($d) => !empty($d->hasMistake),
                                                            );
                                                        @endphp

                                                        @forelse ($mistakes as $delivery)
                                                            <tr>
                                                                <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                                                <td class="px-4 py-3">
                                                                    {{ $delivery->kurir->display_name ?? '-' }}</td>

                                                                <td class="px-4 py-3">
                                                                    {{ optional(optional($delivery->hasMistake)->reportedBy)->display_name ?? 'Tidak ada' }}
                                                                </td>

                                                                <td class="px-4 py-3">
                                                                    {{ optional($delivery->hasMistake)->photo_url ?? '-' }}
                                                                </td>

                                                                <td class="px-4 py-3">
                                                                    {{ optional($delivery->hasMistake)->notes ?? '-' }}
                                                                </td>

                                                                <td class="px-4 py-3">
                                                                    {{ optional($delivery->hasMistake)->reported_at
                                                                        ? \Carbon\Carbon::parse($delivery->hasMistake->reported_at, 'Asia/Jakarta')->format('d F Y H:i') . ' WIB'
                                                                        : 'Belum dilaporkan' }}
                                                                </td>
                                                                <td class="px-4 py-3">
                                                                    {{ optional($delivery->hasMistake->deliveryMistakeConfirmation)->confirmed_at
                                                                        ? \Carbon\Carbon::parse($delivery->hasMistake->deliveryMistakeConfirmation->confirmed_at, 'Asia/Jakarta')->format(
                                                                                'd F Y H:i',
                                                                            ) . ' WIB'
                                                                        : 'Belum dikonfirmasi' }}
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="7"
                                                                    class="text-center py-4 text-gray-500">
                                                                    Tidak ada data
                                                                </td>
                                                            </tr>
                                                        @endforelse

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="px-4 mt-8 mb-4">
                                            <h1 class="font-bold text-center text-l1">Stok barang tersisa</h1>
                                            <div class="mt-2 grid grid-cols-12">
                                                @foreach ($outlet->hasItemSetting->sortBy('pivot.quantity') as $item)
                                                    <div
                                                        class="px-6 py-6 mt-2 shadow-reguler flex gap-5 items-center rounded-lg col-span-12 md:col-span-6 lg:col-span-4">
                                                        <img src="{{ $item->image }}" class="w-[40px]"
                                                            alt="Foto item">
                                                        <div>
                                                            <h2 class="text-reguler font-medium">
                                                                {{ $item->name }}</h2>
                                                            <p class="text-1 text-neutral-300">Tipe:
                                                                {{ $item->type }}</p>
                                                            <p class="text-h3 font-semibold">
                                                                {{ $item->pivot->quantity }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        {{-- End of Outlet --}}

        {{-- Inventaris --}}
        <div class="mt-12 w-full ">
            <h1 class="border-b-2 border-b-primary w-fit font-medium lg:text-l1">Inventaris</h1>
            <div class="mt-4 grid grid-cols-12 gap-2">
                @foreach ($inventories as $inventory)
                    {{-- Tambahkan pengecekan if $inventory->item --}}
                    @if ($inventory->item)
                        <div
                            class="px-6 py-6 mt-2 shadow-reguler flex gap-5 items-center rounded-lg col-span-12 md:col-span-6 lg:col-span-4">
                            <div>
                                {{-- Hapus dd() jika sudah tidak dipakai debugging --}}
                                <h2 class="text-reguler font-medium">{{ $inventory->item->name }}</h2>
                                <p class="text-1 text-neutral-300">Tipe: {{ $inventory->item->type }}</p>
                                <p class="text-h3 font-semibold">{{ $inventory->stock }}</p>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        {{-- End of inventaris --}}

    </div>
</x-layouts.app>
