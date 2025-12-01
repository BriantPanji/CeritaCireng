<div>
    {{-- Header Section --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Stok</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola dan pantau stok barang di gudang</p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        {{-- Total Items --}}
        <div class="bg-white rounded-xl shadow p-4 flex flex-row gap-4 items-center md:flex-col md:items-start">
            <div class="w-16 h-16 bg-primary p-3 rounded-full flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
                    class="w-10 h-10 text-white">
                    <path d="M3 7l9-4 9 4-9 4-9-4z" />
                    <path d="M3 7v10l9 4 9-4V7" />
                    <path d="M12 11v10" />
                </svg>
            </div>

            <div class="flex-1">
                <h3 class="font-semibold text-sm text-gray-800">Total Item</h3>
                <p class="text-xs text-gray-500">Jumlah jenis barang</p>

                <div class="mt-2 md:mt-4">
                    <p class="text-3xl font-bold text-gray-900">{{ $items->total() }}</p>
                    <p class="text-sm text-gray-500">Jenis barang</p>
                </div>
            </div>
        </div>

        {{-- Low Stock Warning --}}
        <div class="bg-white rounded-xl shadow p-4 flex flex-row gap-4 items-center md:flex-col md:items-start">
            <div class="w-16 h-16 bg-red-500 p-3 rounded-full flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
                    class="w-10 h-10 text-white">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>

            <div class="flex-1">
                <h3 class="font-semibold text-sm text-gray-800">Stok Rendah</h3>
                <p class="text-xs text-gray-500">Barang dengan stok < 100</p>

                <div class="mt-2 md:mt-4">
                    <p class="text-3xl font-bold text-gray-900">
                        {{ $items->filter(function($item) {
                            return $item->stock && $item->stock->stock < 100;
                        })->count() }}
                    </p>
                    <p class="text-sm text-gray-500">Item</p>
                </div>
            </div>
        </div>

        {{-- Out of Stock --}}
        <div class="bg-white rounded-xl shadow p-4 flex flex-row gap-4 items-center md:flex-col md:items-start">
            <div class="w-16 h-16 bg-gray-500 p-3 rounded-full flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
                    class="w-10 h-10 text-white">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>

            <div class="flex-1">
                <h3 class="font-semibold text-sm text-gray-800">Stok Habis</h3>
                <p class="text-xs text-gray-500">Barang dengan stok = 0</p>

                <div class="mt-2 md:mt-4">
                    <p class="text-3xl font-bold text-gray-900">
                        {{ $items->filter(function($item) {
                            return $item->stock && $item->stock->stock == 0;
                        })->count() }}
                    </p>
                    <p class="text-sm text-gray-500">Item</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="mb-4 flex flex-col md:flex-row gap-3">
        {{-- Search --}}
        <div class="flex-1">
            <div x-data="{xShow: @entangle('query').live}" class="relative">
                <span onclick="document.getElementById('search-stock').focus()"
                      class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                    <i class="ph ph-magnifying-glass"></i>
                </span>
                <input wire:model.live.debounce.300ms="query" 
                       type="text" 
                       id="search-stock"
                       placeholder="Cari nama barang..."
                       class="w-full pl-10 pr-10 py-2 text-sm ring-[0.5px] ring-gray-400 rounded-xl outline-none hover:ring-primary focus:ring-primary-200">
                <button type="button" 
                        x-show="xShow" 
                        @click="$wire.set('query', '')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <i class="ph ph-x text-lg"></i>
                </button>
            </div>
        </div>

        {{-- Filter by Type --}}
        <select wire:model.live="filterType" class="bg-white border rounded-xl px-4 py-2 shadow-sm text-sm">
            <option value="">Semua Kategori</option>
            <option value="BAHAN_MENTAH">Bahan Mentah</option>
            <option value="BAHAN_PENUNJANG">Bahan Penunjang</option>
            <option value="KEMASAN">Kemasan</option>
        </select>

        {{-- Add Stock Button (Only for inventaris, admin, dev) --}}
        @role('inventaris', 'admin', 'dev')
        <button type="button" wire:click="openAddStockModal"
                class="px-4 py-2 bg-primary text-white rounded-xl hover:bg-primary/90 transition cursor-pointer text-sm font-medium flex items-center gap-2">
            <i class="ph ph-plus-circle text-lg"></i>
            <span class="hidden sm:inline">Tambah Stok</span>
        </button>
        @endrole
    </div>

    {{-- Stock Table --}}
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="p-4 border-b">
            <h4 class="font-semibold">Daftar Stok Barang</h4>
            <p class="text-sm text-gray-400">Informasi lengkap stok barang di gudang</p>
        </div>

        <div class="overflow-x-auto relative">
            <table class="min-w-max w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left cursor-pointer hover:bg-gray-100" 
                            wire:click="sortByColumn('name')">
                            <div class="flex items-center gap-2">
                                Nama Barang
                                @if($sortBy === 'name')
                                    <i class="ph {{ $sortDirection === 'asc' ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left cursor-pointer hover:bg-gray-100" 
                            wire:click="sortByColumn('type')">
                            <div class="flex items-center gap-2">
                                Kategori
                                @if($sortBy === 'type')
                                    <i class="ph {{ $sortDirection === 'asc' ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-3 text-center">Stok</th>
                        <th class="px-4 py-3 text-left">Unit</th>
                        <th class="px-4 py-3 text-right cursor-pointer hover:bg-gray-100" 
                            wire:click="sortByColumn('cost')">
                            <div class="flex items-center justify-end gap-2">
                                Harga Satuan
                                @if($sortBy === 'cost')
                                    <i class="ph {{ $sortDirection === 'asc' ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-3 text-center">Status</th>
                        @role('inventaris', 'admin', 'dev')
                        <th class="px-4 py-3 text-center">Aksi</th>
                        @endrole
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-600">
                                {{ ($items->currentPage() - 1) * $items->perPage() + $index + 1 }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-gray-200">
                                        @if(\Str::startsWith($item->image, 'https://'))
                                            <img src="{{ $item->image }}" 
                                                 alt="{{ $item->name }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <img src="{{ asset('storage/' . $item->image) }}" 
                                                 alt="{{ $item->name }}"
                                                 class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <span class="font-medium">{{ \Str::title($item->name) }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-gray-100 rounded text-xs">
                                    {{ \Str::title(\Str::replace('_', ' ', $item->type)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-semibold
                                    @if($item->stock && $item->stock->stock == 0)
                                        text-red-600
                                    @elseif($item->stock && $item->stock->stock < 100)
                                        text-yellow-600
                                    @else
                                        text-green-600
                                    @endif
                                ">
                                    {{ $item->stock ? number_format($item->stock->stock, 0, ',', '.') : '0' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $item->unit }}</td>
                            <td class="px-4 py-3 text-right font-medium">
                                @convertRupiah($item->cost)
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($item->stock && $item->stock->stock == 0)
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                        Habis
                                    </span>
                                @elseif($item->stock && $item->stock->stock < 100)
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">
                                        Rendah
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                        Aman
                                    </span>
                                @endif
                            </td>
                            @role('inventaris', 'admin', 'dev')
                            <td class="px-4 py-3 text-center">
                                <button type="button" 
                                        wire:click="openReduceStockModal({{ $item->id }})"
                                        class="px-3 py-1 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition text-xs">
                                    Kurangi
                                </button>
                            </td>
                            @endrole
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user() && in_array(auth()->user()->role->name, ['inventaris', 'admin', 'dev']) ? '8' : '7' }}" class="text-center p-8 text-gray-500">
                                @if ($query || $filterType)
                                    <p class="text-lg font-medium">Tidak ada data yang sesuai</p>
                                    <p class="text-sm mt-2">Coba ubah filter atau kata kunci pencarian</p>
                                @else
                                    <p class="text-lg font-medium">Belum ada data stok</p>
                                    <p class="text-sm mt-2">Tambahkan barang untuk memulai</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div wire:loading.flex class="justify-center items-center p-4 text-gray-500 absolute inset-0 bg-white/70 z-10">
                Memuat data...
            </div>
        </div>

        {{-- Pagination --}}
        <div class="overflow-x-auto w-full">
            @if ($items->hasPages())
                <div class="mt-3 flex items-center justify-center gap-2 py-4">
                    {{-- Previous --}}
                    @if ($items->onFirstPage())
                        <button class="px-3 py-2 rounded-xl border border-gray-200 bg-gray-100 text-gray-400 text-sm" disabled>
                            &lt;
                        </button>
                    @else
                        <button wire:click="previousPage"
                            class="px-3 py-2 rounded-xl border border-gray-300 bg-white shadow-sm text-sm text-gray-700 hover:border-primary hover:text-primary transition">
                            &lt;
                        </button>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($items->getUrlRange(1, $items->lastPage()) as $page => $url)
                        @if ($page == $items->currentPage())
                            <button class="px-3 py-2 rounded-xl border border-primary bg-white shadow-sm text-sm text-primary font-semibold">
                                {{ $page }}
                            </button>
                        @else
                            <button wire:click="gotoPage({{ $page }})"
                                class="px-3 py-2 rounded-xl border border-gray-300 bg-white shadow-sm text-sm text-gray-700 hover:border-primary hover:text-primary transition">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if ($items->hasMorePages())
                        <button wire:click="nextPage"
                            class="px-3 py-2 rounded-xl border border-gray-300 bg-white shadow-sm text-sm text-gray-700 hover:border-primary hover:text-primary transition">
                            &gt;
                        </button>
                    @else
                        <button class="px-3 py-2 rounded-xl border bg-gray-100 border-gray-200 text-gray-400 text-sm" disabled>
                            &gt;
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Add Stock Modal --}}
    @if($showAddStockModal)
        <div class="fixed inset-0 bg-black/30 flex items-center justify-center p-4 z-50" 
             wire:click="closeStockModals">
            <div @click.stop class="bg-white rounded-2xl max-w-md w-full shadow-xl">
                <div class="sticky top-0 bg-white border-b border-gray-200 px-4 pt-3 pb-2 flex items-center justify-between rounded-t-2xl">
                    <h2 class="text-lg font-medium">Tambah Stok Barang</h2>
                    <button type="button" wire:click="closeStockModals" title="Tutup Popup"
                            class="cursor-pointer text-gray-400 hover:text-gray-600">
                        <i class="ph ph-x text-2xl"></i>
                    </button>
                </div>

                <form wire:submit.prevent="confirmAddStock">
                    <div class="p-4 space-y-4">
                        <div class="flex flex-col gap-1">
                            <label for="add-item" class="select-none font-medium">Pilih Barang:</label>
                            <select wire:model="selectedItemId" id="add-item" required
                                    class="w-full p-2 ring outline-none focus:ring-gray-600 ring-gray-300 rounded-md">
                                <option value="">-- Pilih Barang --</option>
                                @php
                                    $allItems = \App\Models\Item::orderBy('name')->get();
                                @endphp
                                @foreach($allItems as $itemOption)
                                    <option value="{{ $itemOption->id }}">{{ \Str::title($itemOption->name) }}</option>
                                @endforeach
                            </select>
                            @error('selectedItemId') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col gap-1">
                            <label for="add-amount" class="select-none font-medium">Jumlah Penambahan:</label>
                            <input wire:model="adjustmentAmount" type="number" min="1" id="add-amount" required
                                   class="w-full p-2 ring outline-none focus:ring-gray-600 ring-gray-300 rounded-md"
                                   placeholder="Masukkan jumlah">
                            @error('adjustmentAmount') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-4 py-2.5 flex justify-end gap-2 rounded-b-2xl">
                        <button type="button" wire:click="closeStockModals"
                                class="px-4 py-1 cursor-pointer bg-white border border-gray-300 text-black rounded-lg hover:bg-gray-300 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-1 cursor-pointer bg-primary border border-gray-300 text-black rounded-lg hover:bg-primary/75 transition">
                            Tambah
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Reduce Stock Modal --}}
    @if($showReduceStockModal)
        <div class="fixed inset-0 bg-black/30 flex items-center justify-center p-4 z-50"
             wire:click="closeStockModals">
            <div @click.stop class="bg-white rounded-2xl max-w-md w-full shadow-xl">
                <div class="sticky top-0 bg-white border-b border-gray-200 px-4 pt-3 pb-2 flex items-center justify-between rounded-t-2xl">
                    <h2 class="text-lg font-medium">Kurangi Stok Barang</h2>
                    <button type="button" wire:click="closeStockModals" title="Tutup Popup"
                            class="cursor-pointer text-gray-400 hover:text-gray-600">
                        <i class="ph ph-x text-2xl"></i>
                    </button>
                </div>

                <form wire:submit.prevent="confirmReduceStock">
                    <div class="p-4 space-y-4">
                        <div class="flex flex-col gap-1">
                            <label class="select-none font-medium">Nama Barang:</label>
                            <div class="w-full p-2 bg-gray-100 rounded-md font-medium">
                                {{ \Str::title($selectedItemName) }}
                            </div>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="select-none font-medium">Stok Saat Ini:</label>
                            <div class="w-full p-2 bg-gray-100 rounded-md font-medium">
                                {{ number_format($selectedItemCurrentStock, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label for="reduce-amount" class="select-none font-medium">Jumlah Pengurangan:</label>
                            <input wire:model="adjustmentAmount" type="number" min="1" max="{{ $selectedItemCurrentStock }}" 
                                   id="reduce-amount" required
                                   class="w-full p-2 ring outline-none focus:ring-gray-600 ring-gray-300 rounded-md"
                                   placeholder="Masukkan jumlah">
                            @error('adjustmentAmount') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-4 py-2.5 flex justify-end gap-2 rounded-b-2xl">
                        <button type="button" wire:click="closeStockModals"
                                class="px-4 py-1 cursor-pointer bg-white border border-gray-300 text-black rounded-lg hover:bg-gray-300 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-1 cursor-pointer bg-orange-500 border border-gray-300 text-white rounded-lg hover:bg-orange-600 transition">
                            Kurangi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- SweetAlert Scripts --}}
    @script
    <script>
        // Confirmation for Add Stock
        document.addEventListener('confirm-add-stock', event => {
            Swal.fire({
                title: 'Konfirmasi Tambah Stok',
                html: `Apakah Anda yakin ingin menambah stok <strong>${event.detail.itemName}</strong> sebanyak <strong>${event.detail.amount}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tambah',
                cancelButtonText: 'Batal',
                confirmButtonColor: "#efa800",
                cancelButtonColor: "#6b7280"
            }).then(result => {
                if (result.isConfirmed) {
                    $wire.dispatch('executeAddStock');
                }
            });
        });

        // Confirmation for Reduce Stock
        document.addEventListener('confirm-reduce-stock', event => {
            Swal.fire({
                title: 'Konfirmasi Kurangi Stok',
                html: `Apakah Anda yakin ingin mengurangi stok <strong>${event.detail.itemName}</strong> sebanyak <strong>${event.detail.amount}</strong>?<br><br>Stok saat ini: <strong>${event.detail.currentStock}</strong>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kurangi',
                cancelButtonText: 'Batal',
                confirmButtonColor: "#f97316",
                cancelButtonColor: "#6b7280"
            }).then(result => {
                if (result.isConfirmed) {
                    $wire.dispatch('executeReduceStock');
                }
            });
        });

        // Success notification
        document.addEventListener('stock-updated', event => {
            Swal.fire({
                title: 'Berhasil!',
                text: event.detail.message,
                icon: 'success',
                confirmButtonColor: "#efa800"
            });
        });

        // Error notification
        document.addEventListener('show-error', event => {
            Swal.fire({
                title: 'Error!',
                text: event.detail.message,
                icon: 'error',
                confirmButtonColor: "#990000"
            });
        });
    </script>
    @endscript
</div>
