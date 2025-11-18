@props(['show' => false, 'title', 'item' => null, 'isEdit' => false])

@php
    $itemId = $item ? $item->id : null;
@endphp

<div 
    x-data="{ show: @entangle('show' . ($isEdit ? 'Edit' : 'Add')) }" 
    x-show="show" 
    x-transition.opacity 
    @click.self="show = false"
    class="fixed inset-0 bg-black/30 flex items-center justify-center p-4 z-50"
    style="display: none;"
>
    <div @click.stop class="bg-white no-scrollbar rounded-2xl max-w-3xl w-full xs:w-sm lg:w-3xl max-h-[90vh] overflow-y-auto shadow-xl">
        
        {{-- Header --}}
        <div class="sticky top-0 bg-white border-b border-gray-200 px-4 pt-3 pb-2 flex items-center justify-between rounded-t-2xl">
            <h2 class="text-lg font-medium">{{ $title }}</h2>
            <button type="button" wire:click="closeModal{{ $isEdit ? 'Edit' : 'Add' }}"
                class="cursor-pointer text-gray-400 hover:text-gray-600">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        {{-- Form Content --}}
        <div class="p-6">
            <form wire:submit="{{ $isEdit ? 'updateItem' : 'saveItem' }}" class="space-y-4">
                
                {{-- Image Preview --}}
                @if($item && $item->image)
                    <div class="w-full">
                        <label class="text-sm font-medium text-gray-700 mb-2 block">Gambar Saat Ini</label>
                        <div class="w-full aspect-[4/3] lg:max-w-xs bg-gray-200 rounded-xl overflow-hidden">
                            <img src="{{ $item->image }}" class="w-full h-full object-cover" alt="{{ $item->name }}">
                        </div>
                    </div>
                @endif

                {{-- Image Upload --}}
                <div class="w-full">
                    <label class="text-sm font-medium text-gray-700 mb-2 block">
                        {{ $isEdit ? 'Upload Gambar Baru (Opsional)' : 'Upload Gambar' }}
                    </label>
                    <input type="file" wire:model="{{ $isEdit ? 'newImage' : 'image' }}" accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition">
                    @error($isEdit ? 'newImage' : 'image')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    
                    {{-- Preview new upload --}}
                    @if($isEdit)
                        <div wire:loading wire:target="newImage" class="text-sm text-gray-500 mt-2">Uploading...</div>
                        @if($newImage ?? false)
                            <div class="mt-2">
                                <p class="text-sm text-gray-600 mb-2">Preview Gambar Baru:</p>
                                <div class="w-full aspect-[4/3] lg:max-w-xs bg-gray-200 rounded-xl overflow-hidden">
                                    <img src="{{ $newImage->temporaryUrl() }}" class="w-full h-full object-cover" alt="Preview">
                                </div>
                            </div>
                        @endif
                    @else
                        <div wire:loading wire:target="image" class="text-sm text-gray-500 mt-2">Uploading...</div>
                        @if($image ?? false)
                            <div class="mt-2">
                                <p class="text-sm text-gray-600 mb-2">Preview:</p>
                                <div class="w-full aspect-[4/3] lg:max-w-xs bg-gray-200 rounded-xl overflow-hidden">
                                    <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover" alt="Preview">
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Name --}}
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Nama Barang *</label>
                    <input type="text" wire:model="form.name" placeholder="Contoh: Tepung Terigu"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition">
                    @error('form.name')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Type --}}
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Kategori *</label>
                    <select wire:model="form.type"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition">
                        <option value="">Pilih Kategori</option>
                        <option value="BAHAN_MENTAH">Bahan Mentah</option>
                        <option value="BAHAN_PENUNJANG">Bahan Penunjang</option>
                        <option value="KEMASAN">Kemasan</option>
                    </select>
                    @error('form.type')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cost --}}
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Harga Satuan (Rp) *</label>
                    <input type="number" wire:model="form.cost" min="0" placeholder="Contoh: 15000"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition">
                    @error('form.cost')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Unit --}}
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Satuan *</label>
                    <select wire:model="form.unit"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition">
                        <option value="">Pilih Satuan</option>
                        <option value="pcs">Pcs (Piece)</option>
                        <option value="gr">Gr (Gram)</option>
                        <option value="ml">Ml (Mililiter)</option>
                        <option value="unit">Unit</option>
                    </select>
                    @error('form.unit')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </form>
        </div>

        {{-- Footer --}}
        <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-4 py-2.5 flex justify-end gap-2 rounded-b-2xl">
            <button type="button" wire:click="closeModal{{ $isEdit ? 'Edit' : 'Add' }}"
                class="px-4 py-1 cursor-pointer bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Batal
            </button>
            <button type="button" wire:click="{{ $isEdit ? 'updateItem' : 'saveItem' }}"
                class="px-4 py-1 cursor-pointer bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Barang' }}
            </button>
        </div>
    </div>
</div>
