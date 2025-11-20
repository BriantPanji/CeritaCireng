<div class="p-3">
    {{-- SEARCH --}}
    <div class="mt-4 flex items-center gap-2">
        <div class="flex items-center bg-white shadow-reguler px-3 py-3 rounded-xl flex-1 cursor-pointer">
            <i class="ph ph-magnifying-glass"> </i>
            <input type="text" wire:model.live="search" class="ml-2 w-full text-sm focus:outline-none"
                placeholder="Cari pengantaran">
        </div>
    </div>

    {{-- FILTER --}}
    <div class="mt-4 flex items-center gap-2 overflow-x-auto pb-2">

        {{-- Tanggal --}}
        <select wire:model.live="waktu"
            class="bg-white border border-gray-200 px-4 py-2 rounded-xl shadow-sm text-sm cursor-pointer">
            <option value="today">Hari ini</option>
            <option value="week">1 Minggu</option>
            <option value="month">1 Bulan</option>
            <option value="year">1 Tahun</option>
            <option value="all">Semua</option>
        </select>

        {{-- Kurir --}}
        <select wire:model.live="kurir"
            class="bg-white border border-gray-200 px-4 py-2 rounded-xl shadow-sm text-sm cursor-pointer">
            <option value="">Semua Kurir</option>
            @foreach ($couriers as $courier)
                <option value="{{ $courier->id }}">{{ $courier->display_name }}</option>
            @endforeach
        </select>

        {{-- Status --}}
        <select wire:model.live="status"
            class="bg-white border border-gray-200 px-4 py-2 rounded-xl shadow-sm text-sm cursor-pointer">
            <option value="">Semua Status</option>
            @foreach ($statuses as $item)
                <option value="{{ $item->status }}">{{ $item->status }}</option>
            @endforeach
        </select>

    </div>

    {{-- TABEL --}}
    <div class="mt-4 bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="overflow-x-auto w-full table-container">
            <table class="min-w-max w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 font-semibold text-left w-12">No</th>
                        <th class="px-4 py-3 font-semibold text-left">Nama Kurir</th>
                        <th class="px-4 py-3 font-semibold text-left">Outlet Tujuan</th>
                        <th class="px-4 py-3 font-semibold text-left">Waktu Ditugaskan</th>
                        <th class="px-4 py-3 font-semibold text-left">Waktu Kirim</th>
                        <th class="px-4 py-3 font-semibold text-left">Status</th>
                        <th class="px-4 py-3 font-semibold text-left">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($deliveries as $delivery)
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-3">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3">{{ $delivery->kurir->display_name }}</td>
                            <td class="px-4 py-3">{{ $delivery->outlet->name }}</td>
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($delivery->assigned_at, 'Asia/Jakarta')->format('d F Y, H:i') }}
                                WIB
                            </td>
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($delivery->delivered_at, 'Asia/Jakarta')->format('d F Y, H:i') }}
                                WIB</td>
                            <td class="px-4 py-3 rounded-lg">
                                <p x-data
                                    :class="{
                                        'bg-secondary text-white': '{{ $delivery->status }}'
                                        === 'DIBATALKAN',
                                        'bg-primary-200 text-white': '{{ $delivery->status }}'
                                        === 'DITUGASKAN',
                                        'bg-tertiary text-white': '{{ $delivery->status }}'
                                        === 'SELESAI',
                                        'bg-primary/90 text-white': '{{ $delivery->status }}'
                                        === 'DIKIRIM',
                                    }"
                                    class="p-2 px-3 rounded-lg text-center text-xs lg:text-reguler">
                                    {{ $delivery->status }}
                                </p>
                            </td>

                            <td class="px-4">
                                @if ($delivery->status == 'DITUGASKAN' || $delivery->status == 'DIKIRIM')
                                    <button wire:click="confirmBatal({{ $delivery->id }})"
                                        class="bg-secondary hover:bg-secondary/90 text-white px-3 py-2 rounded-md shadow-button text-xs cursor-pointer lg:text-reguler">
                                        Batalkan</button>
                                @else
                                    <button
                                        class="bg-neutral-200 text-white px-3 py-2 rounded-md shadow-button text-xs cursor-not-allowed lg:text-reguler">
                                        Batalkan</button>
                                @endif
                                <span x-data="{ modalIsOpen: false }">
                                    <button x-on:click="modalIsOpen = true" type="button"
                                        class="bg-primary text-white px-3 py-2   rounded-md shadow-button text-xs ml-2 cursor-pointer mr-4 hover:bg-primary/90 lg:text-reguler">Detail</button>
                                    <div x-cloak x-show="modalIsOpen" x-transition.opacity.duration.200ms
                                        x-trap.inert.noscroll="modalIsOpen"
                                        x-on:keydown.esc.window="modalIsOpen = false"
                                        x-on:click.self="modalIsOpen = false"
                                        class="fixed inset-0 z-30 flex items-center justify-center p-4 pb-8 lg:p-8 bg-neutral-500/30 backdrop-blur-xs"
                                        x-transition.opacity role="dialog" aria-modal="true"
                                        aria-labelledby="defaultModalTitle">
                                        <!-- Modal Dialog -->
                                        <div x-show="modalIsOpen"
                                            x-transition:enter="transition ease-out duration-200 delay-100 motion-reduce:transition-opacity"
                                            x-transition:enter-start="opacity-0 translate-y-8"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            class="flex flex-col gap-4 overflow-x-hidden overflow-y-scroll h-[400px] rounded-radius border border-outline bg-white w-[90%] md:max-w-[600px]">
                                            <!-- Dialog Header -->
                                            <div class="flex items-center justify-between border-b border-outline p-4">
                                                <h3 id="defaultModalTitle" class="font-semibold tracking-wide text-l2">
                                                    Detail Pengantaran</h3>
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
                                            <div class="px-4 py-8 space-y-3 text-xs lg:text-1">
                                                <div class="flex justify-between relative">
                                                    <p
                                                        class="before:content-[':'] font-semibold before:block before:left-35 md:before:left-50 before:absolute">
                                                        Nama Inventaris</p>
                                                    <p class="w-[60%] text-left">
                                                        {{ $delivery->inventaris->display_name }}
                                                    </p>
                                                </div>
                                                <div class="flex justify-between relative">
                                                    <p
                                                        class="before:content-[':'] font-semibold before:block before:left-35 md:before:left-50 before:absolute">
                                                        Nama Kurir</p>
                                                    <p class="w-[60%] text-left">
                                                        {{ $delivery->kurir->display_name }}
                                                    </p>
                                                </div>
                                                <div class="flex justify-between relative">
                                                    <p
                                                        class="before:content-[':'] font-semibold before:block before:left-35 md:before:left-50 before:absolute">
                                                        Outlet</p>
                                                    <p class="w-[60%] text-left">
                                                        {{ $delivery->outlet->name }}</p>
                                                </div>
                                                <div class="flex justify-between relative">
                                                    <p
                                                        class="before:content-[':'] font-semibold before:block before:left-35 md:before:left-50 before:absolute">
                                                        Status</p>
                                                    <p x-data
                                                        :class="{
                                                            'text-secondary': '{{ $delivery->status }}'
                                                            === 'DIBATALKAN',
                                                            'text-primary-200': '{{ $delivery->status }}'
                                                            === 'DITUGASKAN',
                                                            'text-tertiary': '{{ $delivery->status }}'
                                                            === 'SELESAI',
                                                            'text-primary/90': '{{ $delivery->status }}'
                                                            === 'DIKIRIM',
                                                        }"
                                                        class="w-[60%] text-left">
                                                        {{ $delivery->status }}
                                                    </p>
                                                </div>
                                                <div class="flex justify-between relative">
                                                    <p
                                                        class="before:content-[':'] font-semibold before:block before:left-35 md:before:left-50 before:absolute">
                                                        Waktu Penugasan</p>
                                                    <p class="w-[60%] text-left">
                                                        {{ \Carbon\Carbon::parse($delivery->assigned_at, 'Asia/Jakarta')->format('d F Y, H:i') }}
                                                        WIB
                                                    </p>
                                                </div>
                                                <div class="flex justify-between relative">
                                                    <p
                                                        class="before:content-[':'] font-semibold before:block before:left-35 md:before:left-50 before:absolute">
                                                        Waktu Pengiriman</p>
                                                    <p class="w-[60%] text-left">
                                                        {{ \Carbon\Carbon::parse($delivery->delivered_at, 'Asia/Jakarta')->format('d F Y, H:i') }}
                                                        WIB
                                                    </p>
                                                </div>
                                                <div class="flex justify-between relative">
                                                    <p
                                                        class="before:content-[':'] font-semibold before:block before:left-35 md:before:left-50 before:absolute">
                                                        Waktu Penerimaan</p>
                                                    @if ($delivery->hasDeliveryConfirmation)
                                                        <p class="w-[60%] text-left">
                                                            {{ \Carbon\Carbon::parse($delivery->hasDeliveryConfirmation->received_at, 'Asia/Jakarta')->format('d F Y, H:i') }}
                                                            WIB
                                                        </p>
                                                    @else
                                                        <p class="w-[60%] text-left">Belum
                                                            diterima</p>
                                                    @endif
                                                </div>
                                                <div class="flex justify-between relative">
                                                    <p
                                                        class="before:content-[':'] font-semibold before:block before:left-35 md:before:left-50 before:absolute">
                                                        Staff yang menerima</p>
                                                    @if ($delivery->hasDeliveryConfirmation)
                                                        <p class="w-[60%] text-left">
                                                            {{ $delivery->hasDeliveryConfirmation->staff->display_name }}
                                                        </p>
                                                    @else
                                                        <p class="w-[60%] text-left">Belum
                                                            diterima</p>
                                                    @endif
                                                </div>
                                                <div class="flex justify-between relative">
                                                    <p
                                                        class="before:content-[':'] font-semibold before:block before:left-35 md:before:left-50 before:absolute">
                                                        Notes</p>
                                                    <p class="w-[60%] text-left">
                                                        {{ $delivery->hasDeliveryConfirmation?->notes ?? '-' }}
                                                    </p>
                                                </div>
                                                <div class="flex  flex-col justify-between relative">
                                                    <p
                                                        class="before:content-[':'] font-semibold before:block before:left-35 md:before:left-50 before:absolute">
                                                        Bukti Photo</p>
                                                    <img src={{ asset('images/Conan.jpg') }} alt="Bukti Foto"
                                                        class="w-[300px] mx-auto mt-8">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <th colspan="7" class="text-center px-4 py-8">
                                {{-- <img src={{ asset('images/search-no-result.png') }} alt="Search not Avaiable"
                                    width="200" class="grayscale-100 mx-auto"> --}}
                                <p class="font-semibold text-neutral-300">Pengantaran tidak tersedia</p>
                            </th>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- CUSTOM PAGINATION --}}
    @if ($deliveries->hasPages())
        <div class="mt-3 flex items-center justify-center gap-2 py-4 flex-col w-full">

            <div class="flex gap-2">
                {{-- Previous --}}
                @if ($deliveries->onFirstPage())
                    <button class="px-3 py-2 rounded-xl text-neutral-300 text-reguler cursor-not-allowed" disabled>
                        &lt;
                    </button>
                @else
                    <button wire:click="previousPage"
                        class="px-3 py-2 rounded-xl text-reguler
                        hover:border-primary hover:text-primary duration-300">
                        &lt;
                    </button>
                @endif

                {{-- Page Numbers --}}
                @foreach ($pages as $p)
                    <div wire:key="page-btn-{{ $p }}">

                        @if ($p == $deliveries->currentPage())
                            <button
                                class="w-11 flex justify-center text-center px-4 py-2 rounded-lg border border-primary text-primary font-semibold">
                                {{ $p }}
                            </button>
                        @else
                            <button wire:click="gotoPage({{ $p }})"
                                class="w-11 flex justify-center text-center px-4 py-2 rounded-lg hover:bg-neutral-50 duration-300">
                                {{ $p }}
                            </button>
                        @endif

                    </div>
                @endforeach

                {{-- Next --}}
                @if ($deliveries->hasMorePages())
                    <button wire:click="nextPage"
                        class="px-3 py-2 rounded-xl text-reguler
                        hover:border-primary hover:text-primary duration-300">
                        &gt;
                    </button>
                @else
                    <button
                        class="px-3 py-2 rounded-xlborder-gray-200 text-neutral-300 text-reguler cursor-not-allowed"
                        disabled>
                        &gt;
                    </button>
                @endif
            </div>

            <h1 class="text-neutral-300 text-1 lg:text-reguler">Menampilkan {{ $deliveries->count() }} data dari total
                {{ $deliveries->total() }}
                data.</h1>

        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:init', function() {

        // Konfirmasi sebelum batalkan
        // Listener 'Livewire.on' masih berfungsi di v3
        Livewire.on('confirmBatal', (event) => {

            // 3. Ambil data 'deliveryId' dari 'event.deliveryId'
            const deliveryId = event.deliveryId;

            Swal.fire({
                title: 'Yakin ingin membatalkan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#FFB504',
                confirmButtonText: 'Ya, batalkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {

                    // 2. GANTI 'Livewire.emit' MENJADI 'Livewire.dispatch'
                    // Kirim sebagai objek dengan key 'deliveryId'
                    Livewire.dispatch('batalkan', {
                        deliveryId: deliveryId
                    });
                }
            });
        });

        // Notifikasi setelah dibatalkan
        // 'Livewire.on' juga masih berfungsi di sini
        Livewire.on('deliveryBatal', (event) => {
            Swal.fire(
                'Dibatalkan!',
                'Pengantaran berhasil dibatalkan.',
                'success'
            );
        });
    });
</script>
