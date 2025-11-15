<div>
    <div class="px-6 py-5  bg-white rounded-xl m-4 shadow-sm">
        <h1 class="font-semibold ml-4 mb-4 text-dark">Tambah Barang</h1>
        
        <form wire:submit.prevent="save">
            
            {{-- FOTO --}}
            <div wire:loading wire:target="image" class="text-sm text-gray-500 mt-2">
                Mengunggah gambar...
            </div>
            <div class="flex items-center justify-center w-full my-2">
                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 bg-neutral-secondary-medium border-primary-50 border-1 rounded-lg cursor-pointer hover:bg-neutral-tertiary-medium">
                    @if ($image)
                        <div class="flex flex-col items-center justify-center">
                            <img src="{{ $image->temporaryUrl() }}" class="h-48 mb-4 rounded object-cover">
                            <p class="text-sm text-gray-600">Klik untuk mengganti gambar</p>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center text-body pb-6">
                            <span class="ph ph-package text-primary-200 text-5xl py-4"></span>
                            <p class="mb-2 text-sm"><span class="font-semibold">Klik untuk mengunggah</span> atau seret
                                dan
                                lepas</p>
                            <p class="text-xs">PNG atau JPG (Maks: 2 MB)</p>
                        </div>
                    @endif
                    <input id="dropzone-file" type="file" class="hidden" wire:model="image" accept="image/*">
                </label>
            </div>
            @error('image')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            {{-- NAMA --}}
            <input type="text" wire:model="name" placeholder="Nama Barang"
                class="w-full px-3 py-2 rounded-lg border border-primary-50 focus:border-primary-200 outline-none">
            @error('name')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror

            {{-- KATEGORI (TYPE) --}}
            <div class="px-3 py-2 mt-2 rounded-lg border border-primary-50 focus:border-primary-200">
                <select wire:model="type" class="w-full focus:outline-none">
                    <option value="">Pilih Kategori</option>
                    <option value="BAHAN_MENTAH">Bahan Mentah</option>
                    <option value="BAHAN_PENUNJANG">Bahan Penunjang</option>
                    <option value="KEMASAN">Kemasan</option>
                </select>
            </div>
            @error('type')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror

            {{-- HARGA --}}
            <input type="number" wire:model="cost" placeholder="Harga Satuan" min="0"
                class="w-full px-3 py-2  mt-2 rounded-lg border border-primary-50 focus:border-primary-200 outline-none">
            @error('cost')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror

            {{-- SATUAN --}}
            <div class="px-3 py-2 mt-2 rounded-lg border border-primary-50">
                <select wire:model="unit" class="w-full focus:outline-none">
                    <option value="">Pilih Satuan</option>
                    <option value="pcs">Pcs</option>
                    <option value="gr">Gram</option>
                    <option value="ml">Mililiter</option>
                    <option value="unit">Unit</option>
                </select>
            </div>
            @error('unit')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror

            <div class="mt-16 flex gap-4 flex-end align-self-stretch">
                <div class="w-full">
                </div>
                <button type="button"
                    class="w-full bg-gray-200 border-1 border-gray-400 text-dark py-2 rounded-lg hover:bg-gray-300"
                    onclick="window.location='{{ url('/inventory') }}'">
                    Batal
                </button>
                <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg p-4">
                    Tambahkan
                </button>
            </div>
        </form>

    </div>
</div>
