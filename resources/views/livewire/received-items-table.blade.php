<div>
    {{-- SEARCH AND FILTER --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 mb-4">
        <div class="flex items-center bg-white shadow-reguler px-3 py-3 rounded-xl flex-1 cursor-pointer">
            <i class="ph ph-magnifying-glass"> </i>
            <input type="text" wire:model.live.debounce.300ms="search" class="ml-2 w-full text-sm focus:outline-none"
                placeholder="Cari barang...">
        </div>
        <select wire:model.live="waktu"
            class="bg-white border border-neutral-200 px-3 py-3 rounded-xl shadow-sm text-sm cursor-pointer">
            <option value="today">Hari ini</option>
            <option value="week">1 Minggu</option>
            <option value="month">1 Bulan</option>
            <option value="year">1 Tahun</option>
            <option value="all">Semua</option>
        </select>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-max">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 font-semibold text-left">Barang</th>
                        <th class="px-4 py-3 font-semibold text-left">Jumlah</th>
                        <th class="px-4 py-3 font-semibold text-left">Waktu Penerimaan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($receivedItems as $item)
                    <tr class="border-b">
                        <td class="px-4 py-3">{{ $item['item_name'] }}</td>
                        <td class="px-4 py-3">{{ $item['quantity'] }}</td>
                        <td class="px-4 py-3">
                            {{ $item['received_at'] 
                                ? \Carbon\Carbon::parse($item['received_at'])->timezone('Asia/Jakarta')->format('d F Y, H:i') . ' WIB'
                                : 'Belum terkonfirmasi' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <th colspan="3" class="text-center px-4 py-8">
                            <p class="font-semibold text-neutral-300">Barang belum tersedia</p>
                        </th>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- CUSTOM PAGINATION --}}
    @if ($receivedItems->hasPages())
        <div class="mt-3 flex items-center justify-center gap-2 py-4 flex-col w-full">

            <div class="flex gap-2">
                {{-- Previous --}}
                @if ($receivedItems->onFirstPage())
                    <button class="px-3 py-2 rounded-xl text-neutral-300 text-reguler cursor-not-allowed" disabled>
                        &lt;
                    </button>
                @else
                    <button wire:click="previousPage"
                        class="px-3 py-2 rounded-xl text-reguler hover:border-primary hover:text-primary duration-300">
                        &lt;
                    </button>
                @endif

                {{-- Page Numbers --}}
                @php
                    $currentPage = $receivedItems->currentPage();
                    $lastPage = $receivedItems->lastPage();
                    $show = 3;
                    
                    if ($lastPage <= $show) {
                        $pages = range(1, $lastPage);
                    } else {
                        $start = $currentPage - 1;
                        $end = $currentPage + 1;
                        
                        if ($start < 1) {
                            $start = 1;
                            $end = $show;
                        }
                        
                        if ($end > $lastPage) {
                            $end = $lastPage;
                            $start = $lastPage - ($show - 1);
                        }
                        
                        $pages = range($start, $end);
                    }
                @endphp

                @foreach ($pages as $p)
                    <div wire:key="page-btn-{{ $p }}">
                        @if ($p == $currentPage)
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
                @if ($receivedItems->hasMorePages())
                    <button wire:click="nextPage"
                        class="px-3 py-2 rounded-xl text-reguler hover:border-primary hover:text-primary duration-300">
                        &gt;
                    </button>
                @else
                    <button
                        class="px-3 py-2 rounded-xl text-neutral-300 text-reguler cursor-not-allowed"
                        disabled>
                        &gt;
                    </button>
                @endif
            </div>

            <h1 class="text-neutral-300 text-1 lg:text-reguler">Menampilkan {{ $receivedItems->count() }} data dari
                total
                {{ $receivedItems->total() }}
                data.</h1>

        </div>
    @endif
</div>
