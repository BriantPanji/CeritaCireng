<div>
    {{-- Header Section --}}
    <div class="mb-6">
        <h1 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900">Tambah Penugasan Pengiriman</h1>
        <p class="text-xs md:text-sm text-gray-500 mt-1">Buat penugasan baru untuk kurir mengantar barang ke outlet</p>
    </div>

    {{-- Error Message --}}
    @if (session()->has('error'))
    <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-xl mb-4">
        {{ session('error') }}
    </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <form wire:submit.prevent="save"
            @keydown.enter.prevent="if ($event.target.tagName === 'INPUT' && $event.target.type === 'number') { $wire.addItemRow() }">
            <div class="p-6 space-y-6">

                {{-- Courier Selection --}}
                <div>
                    <label for="id_kurir" class="block text-sm font-semibold text-gray-700 mb-2">
                        Kurir <span class="text-red-500">*</span>
                    </label>
                    <select id="id_kurir" wire:model="id_kurir"
                        class="w-full bg-white border rounded-xl outline-none px-4 py-3 shadow-sm text-sm focus:ring focus:ring-yellow-500 focus:border-yellow-500 @error('id_kurir') border-red-500 @enderror">
                        <option value="">-- Pilih Kurir --</option>
                        @foreach($couriers as $courier)
                        <option value="{{ $courier->id }}">{{ $courier->display_name }}</option>
                        @endforeach
                    </select>
                    @error('id_kurir')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Outlet Selection --}}
                <div>
                    <label for="id_outlet" class="block text-sm font-semibold text-gray-700 mb-2">
                        Outlet Tujuan <span class="text-red-500">*</span>
                    </label>
                    <select id="id_outlet" wire:model.live="id_outlet"
                        class="w-full bg-white border rounded-xl px-4 py-3 shadow-sm text-sm focus:ring outline-none focus:ring-yellow-500 focus:border-yellow-500 @error('id_outlet') border-red-500 @enderror">
                        <option value="">-- Pilih Outlet --</option>
                        @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}">{{ $outlet->name }} - {{ $outlet->location }}</option>
                        @endforeach
                    </select>
                    @error('id_outlet')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @if($id_outlet)
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="text-green-600">✓</span> Item default outlet akan otomatis dimuat
                    </p>
                    @endif
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Barang</h3>
                        <button type="button" wire:click="addItemRow"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition flex items-center gap-2">
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
                    <div class="space-y-4">
                        @foreach($deliveryItems as $index => $deliveryItem)
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <div class="flex flex-col md:flex-row gap-4">
                                {{-- Item Selection --}}
                                <div class="flex-1">
                                    <label for="item_{{ $index }}" class="block text-xs font-medium text-gray-700 mb-1">
                                        Barang <span class="text-red-500">*</span>
                                    </label>
                                    <select id="item_{{ $index }}" wire:model="deliveryItems.{{ $index }}.id_item"
                                        class="w-full bg-white outline-none border rounded-lg px-3 py-2 text-sm focus:ring focus:ring-yellow-500 focus:border-yellow-500 @error('deliveryItems.'.$index.'.id_item') border-red-500 @enderror">
                                        <option value="">-- Pilih / Cari Barang --</option>
                                        @foreach($items as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->name }} ({{ $item->unit }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('deliveryItems.'.$index.'.id_item')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Quantity Input --}}
                                @php
                                $selectedItemId = $deliveryItem['id_item'] ?? null;
                                $maxStock = $selectedItemId && isset($itemStocks[$selectedItemId]) ?
                                $itemStocks[$selectedItemId] : 9999;
                                $currentQty = (int)($deliveryItem['quantity'] ?? 0);
                                $exceedsStock = $selectedItemId && $currentQty > $maxStock;
                                @endphp
                                <div class="w-full md:w-40">
                                    <label for="quantity_{{ $index }}"
                                        class="block text-xs font-medium text-gray-700 mb-1">
                                        Jumlah <span class="text-red-500">*</span>
                                        @if($selectedItemId && $maxStock < 9999) <span class="text-gray-400">(max: {{
                                            $maxStock }})</span>
                                            @endif
                                    </label>
                                    <input type="number" id="quantity_{{ $index }}"
                                        wire:model.live="deliveryItems.{{ $index }}.quantity" min="1"
                                        max="{{ $maxStock }}"
                                        class="w-full bg-white border rounded-lg px-3 py-2 text-sm focus:ring outline-none focus:ring-yellow-500 focus:border-yellow-500 @error('deliveryItems.'.$index.'.quantity') border-red-500 @enderror {{ $exceedsStock ? 'border-red-500 bg-red-50' : '' }}"
                                        placeholder="0" />
                                    @error('deliveryItems.'.$index.'.quantity')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    @if($exceedsStock)
                                    <p class="text-red-500 text-xs mt-1">Melebihi stok tersedia ({{ $maxStock }})</p>
                                    @endif
                                </div>

                                {{-- Remove Button --}}
                                @if(count($deliveryItems) > 1)
                                <div class="flex items-end">
                                    <button type="button" wire:click="removeItemRow({{ $index }})"
                                        class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg transition"
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
                        @endforeach
                    </div>

                    @if(count($deliveryItems) === 0)
                    <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded-xl text-sm">
                        <strong>Perhatian:</strong> Minimal harus ada satu item dalam pengiriman.
                    </div>
                    @endif
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('delivery.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2.5 rounded-xl font-medium transition">
                    Batal
                </a>
                <button type="submit"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2.5 rounded-xl font-medium transition flex items-center gap-2"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Simpan Penugasan</span>
                    <span wire:loading wire:target="save" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </div>

    {{-- Success Modal --}}
    @if($showSuccessModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" wire:click="closeSuccessModal"></div>

        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-auto relative z-10 overflow-hidden">
            {{-- Success Icon --}}
            <div class="bg-green-500 p-6 text-center">
                <div class="w-16 h-16 bg-white rounded-full mx-auto flex items-center justify-center">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>

            {{-- Modal Content --}}
            <div class="p-6 text-center">
                <h3 class="text-xl font-bold text-gray-900 mb-2">Penugasan Berhasil Dibuat!</h3>
                <p class="text-gray-600 mb-1">Penugasan pengiriman telah berhasil disimpan.</p>
                <p class="text-sm text-gray-500">ID Pengiriman: <strong>#{{ $createdDeliveryId }}</strong></p>
            </div>

            {{-- Modal Actions --}}
            <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row gap-3">
                <button wire:click="closeSuccessModal"
                    class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2.5 rounded-xl font-medium transition">
                    Buat Penugasan Baru
                </button>
                <a href="{{ route('delivery.index') }}"
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2.5 rounded-xl font-medium transition text-center">
                    Lihat Daftar Pengiriman
                </a>
            </div>
        </div>
    </div>
    @endif
</div>