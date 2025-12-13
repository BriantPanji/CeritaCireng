<div class="p-3">
    {{-- SEARCH --}}
    <div class="mt-12 flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
        <div class="flex items-center bg-white shadow-reguler px-3 py-3 rounded-xl flex-1 cursor-pointer">
            <i class="ph ph-magnifying-glass"> </i>
            <input type="text" wire:model.live="search" class="ml-2 w-full text-sm focus:outline-none"
                placeholder="Cari pengembalian">
        </div>
        @if($isStaff)
            <button @click="$wire.openCreateModal()"
                class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-3 rounded-xl text-sm font-medium transition flex items-center justify-center gap-2 whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                Buat Pengembalian
            </button>
        @endif
    </div>

    {{-- FILTER --}}
    <div class="mt-4 flex items-center gap-2 overflow-x-auto pb-2">
        <select wire:model.live="waktu"
            class="bg-white border border-neutral-200 px-3 py-1 rounded-xl shadow-sm text-sm cursor-pointer">
            <option value="today">Hari ini</option>
            <option value="week">1 Minggu</option>
            <option value="month">1 Bulan</option>
            <option value="year">1 Tahun</option>
            <option value="all">Semua</option>
        </select>
    </div>

    {{-- TABLE --}}
    <div class="mt-4 bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-max">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 font-semibold text-left w-12">No</th>
                        <th class="px-4 py-3 font-semibold text-left">Staff</th>
                        <th class="px-4 py-3 font-semibold text-left">Waktu Pengembalian</th>
                        <th class="px-4 py-3 font-semibold text-left">Waktu Dikonfirmasi</th>
                        <th class="px-4 py-3 font-semibold text-center">Status</th>
                        <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($returns as $return)
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-3 text-1">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 text-1">{{ $return->staff->display_name }}</td>
                            <td class="px-4 py-3 text-1">
                                {{ \Carbon\Carbon::parse($return->returned_at, 'Asia/Jakarta')->format('d F Y, H:i') }}
                                WIB
                            </td>
                            <td class="px-4 py-3 text-1">
                                @if($return->returnConfirmations->count() > 0)
                                    {{ \Carbon\Carbon::parse($return->returnConfirmations->first()->confirmed_at, 'Asia/Jakarta')->format('d F Y, H:i') }} WIB
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 rounded-lg text-1">
                                @if($return->returnConfirmations->count() > 0)
                                    <p class="border border-green-500 text-green-500 p-2 px-3 rounded-xl text-center text-xs lg:text-1">
                                        DIKONFIRMASI
                                    </p>
                                @else
                                    <p class="border border-yellow-500 text-yellow-500 p-2 px-3 rounded-xl text-center text-xs lg:text-1">
                                        PENDING
                                    </p>
                                @endif
                            </td>

                            <td class="px-4">
                                {{-- Admin: Confirm button --}}
                                @if($isAdmin)
                                    @if($return->returnConfirmations->count() == 0)
                                        <button onclick="confirmReturnItem({{ $return->id }})"
                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-xl shadow-button text-xs cursor-pointer lg:text-1">
                                            Konfirmasi</button>
                                    @else
                                        <button disabled
                                            class="bg-neutral-200 text-white px-3 py-2 rounded-xl shadow-button text-xs cursor-not-allowed lg:text-1">
                                            Konfirmasi</button>
                                    @endif
                                @endif

                                {{-- Detail Modal --}}
                                <span x-data="{ modalIsOpen: false }">
                                    <button x-on:click="modalIsOpen = true" type="button"
                                        class="bg-primary text-white px-3 py-2 rounded-xl shadow-button text-xs ml-2 cursor-pointer mr-4 hover:bg-primary/90 lg:text-1">Detail</button>
                                    <div x-cloak x-show="modalIsOpen" x-transition.opacity.duration.200ms
                                        x-trap.inert.noscroll="modalIsOpen"
                                        x-on:keydown.esc.window="modalIsOpen = false"
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
                                            class="flex flex-col gap-4 overflow-x-hidden overflow-y-scroll h-[500px] mt-24 rounded-radius border border-outline bg-white w-[90%] xl:max-w-[700px]">
                                            {{-- Modal Header --}}
                                            <div class="flex items-center justify-between border-b border-outline p-4">
                                                <h3 class="font-semibold tracking-wide text-l2">Detail Pengembalian</h3>
                                                <button x-on:click="modalIsOpen = false" aria-label="close modal">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                        stroke="currentColor" fill="none" stroke-width="1.4" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            {{-- Modal Body --}}
                                            <div class="px-4 py-8 space-y-3 text-xs lg:text-1">
                                                <div class="flex justify-between relative">
                                                    <p class="font-semibold">Staff</p>
                                                    <p class="w-[60%] text-left">{{ $return->staff->display_name }}</p>
                                                </div>
                                                <div class="flex justify-between relative">
                                                    <p class="font-semibold">Waktu</p>
                                                    <p class="w-[60%] text-left">
                                                        {{ \Carbon\Carbon::parse($return->returned_at)->format('d F Y, H:i') }} WIB
                                                    </p>
                                                </div>
                                                <div class="flex justify-between relative">
                                                    <p class="font-semibold">Catatan</p>
                                                    <p class="w-[60%] text-left">{{ $return->notes ?? '-' }}</p>
                                                </div>

                                                {{-- Items Section --}}
                                                <div class="mt-6">
                                                    <p class="font-semibold text-base mb-3 border-b pb-2">Daftar Barang</p>
                                                    <div class="overflow-x-auto">
                                                        <table class="w-full text-xs lg:text-1">
                                                            <thead class="bg-gray-50">
                                                                <tr>
                                                                    <th class="px-3 py-2 text-left font-semibold border-b">No</th>
                                                                    <th class="px-3 py-2 text-left font-semibold border-b">Nama Barang</th>
                                                                    <th class="px-3 py-2 text-center font-semibold border-b">Jumlah</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($return->returnItem as $index => $item)
                                                                    <tr class="border-b last:border-b-0 hover:bg-gray-50">
                                                                        <td class="px-3 py-2">{{ $index + 1 }}</td>
                                                                        <td class="px-3 py-2">{{ $item->name }}</td>
                                                                        <td class="px-3 py-2 text-center font-semibold">{{ $item->pivot->quantity }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <th colspan="6" class="text-center px-4 py-8">
                                <p class="font-semibold text-neutral-300">Pengembalian tidak tersedia</p>
                            </th>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- CUSTOM PAGINATION --}}
    @if ($returns->hasPages())
        <div class="mt-3 flex items-center justify-center gap-2 py-4 flex-col w-full">

            <div class="flex gap-2">
                {{-- Previous --}}
                @if ($returns->onFirstPage())
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
                    $currentPage = $returns->currentPage();
                    $lastPage = $returns->lastPage();
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
                @if ($returns->hasMorePages())
                    <button wire:click="nextPage"
                        class="px-3 py-2 rounded-xl text-reguler hover:border-primary hover:text-primary duration-300">
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

            <h1 class="text-neutral-300 text-1 lg:text-reguler">Menampilkan {{ $returns->count() }} data dari
                total
                {{ $returns->total() }}
                data.</h1>

        </div>
    @endif

    {{-- Create Return Modal --}}
    @if($isStaff)
    <div x-data="{ modalOpen: @entangle('showCreateModal') }" x-cloak>
        <div x-show="modalOpen" 
             x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4 bg-black/50 backdrop-blur-sm"
             @click.self="$wire.closeCreateModal()">
            <div x-show="modalOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[95vh] sm:max-h-[90vh] overflow-hidden flex flex-col">
                
                {{-- Modal Header --}}
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-4 sm:p-6 text-white">
                    <h3 class="text-lg sm:text-2xl font-bold">Buat Pengembalian Barang</h3>
                    <p class="text-xs sm:text-sm opacity-90 mt-1">Isi form untuk mengembalikan barang ke inventory</p>
                </div>

                {{-- Modal Body --}}
                <div class="overflow-y-auto flex-1">
                    <form wire:submit.prevent="submitReturn" id="returnForm">
                        <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                            {{-- Items Section --}}
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Daftar Barang</h3>
                                    <button type="button" wire:click="addItem"
                                        class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Tambah Item
                                    </button>
                                </div>

                                {{-- Items List --}}
                                <div class="space-y-3 sm:space-y-4">
                                    @foreach($selectedItems as $index => $item)
                                    <div class="bg-gray-50 p-3 sm:p-4 rounded-xl border border-gray-200" style="position: relative; overflow: visible;">
                                        <div class="flex flex-col gap-3">
                                            {{-- Item Selection --}}
                                            <div class="w-full relative">
                                                <label for="return_item_{{ $index }}" class="block text-xs font-medium text-gray-700 mb-1">
                                                    Barang <span class="text-red-500">*</span>
                                                </label>
                                                <select id="return_item_{{ $index }}" wire:model="selectedItems.{{ $index }}.id_item"
                                                    class="w-full bg-white outline-none border rounded-lg px-3 py-2 text-sm focus:ring focus:ring-yellow-500 focus:border-yellow-500">
                                                    <option value="">-- Pilih Barang --</option>
                                                    @foreach($items as $availableItem)
                                                    <option value="{{ $availableItem->id }}">
                                                        {{ $availableItem->name }} ({{ $availableItem->unit }})
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Quantity and Remove Button Row --}}
                                            <div class="flex gap-2">
                                                {{-- Quantity Input --}}
                                                <div class="flex-1">
                                                    <label for="return_quantity_{{ $index }}"
                                                        class="block text-xs font-medium text-gray-700 mb-1">
                                                        Jumlah <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="number" id="return_quantity_{{ $index }}"
                                                        wire:model="selectedItems.{{ $index }}.quantity" min="1"
                                                        class="w-full bg-white border rounded-lg px-3 py-2 text-sm focus:ring outline-none focus:ring-yellow-500 focus:border-yellow-500"
                                                        placeholder="0" />
                                                </div>

                                                {{-- Remove Button --}}
                                                @if(count($selectedItems) > 1)
                                                <div class="flex items-end">
                                                    <button type="button" wire:click="removeItem({{ $index }})"
                                                        class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg transition h-[38px] w-[38px] flex items-center justify-center"
                                                        title="Hapus item">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                            fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                @if(count($selectedItems) === 0)
                                <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded-xl text-xs sm:text-sm">
                                    <strong>Perhatian:</strong> Minimal harus ada satu item dalam pengembalian.
                                </div>
                                @endif
                            </div>

                            {{-- Notes --}}
                            <div>
                                <label for="return_notes" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Catatan (Opsional)
                                </label>
                                <textarea id="return_notes" wire:model="notes"
                                          class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 text-sm"
                                          rows="3" placeholder="Tambahkan catatan pengembalian..."></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Modal Footer --}}
                <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-200 flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3">
                    <button type="button" 
                            @click="$wire.closeCreateModal()"
                            class="w-full sm:w-auto bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 sm:px-6 py-2.5 rounded-xl font-medium transition text-sm">
                        Batal
                    </button>
                    <button type="button"
                            onclick="document.getElementById('returnForm').dispatchEvent(new Event('submit', {cancelable: true}))"
                            class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-white px-4 sm:px-6 py-2.5 rounded-xl font-medium transition flex items-center justify-center gap-2 text-sm">
                        <span>Simpan Pengembalian</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Scripts --}}
    <script>
        function confirmReturnItem(returnId) {
            Swal.fire({
                title: 'Konfirmasi Pengembalian?',
                text: "Barang akan ditambahkan kembali ke inventory!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Konfirmasi!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('confirmReturn', {
                        returnId: returnId
                    });
                }
            });
        }

        document.addEventListener('livewire:init', function() {
            // Success notifications
            Livewire.on('returnCreated', () => {
                Swal.fire('Berhasil!', 'Pengembalian berhasil dibuat.', 'success');
            });

            Livewire.on('returnConfirmed', () => {
                Swal.fire('Berhasil!', 'Pengembalian berhasil dikonfirmasi.', 'success');
            });
        });
    </script>
</div>
