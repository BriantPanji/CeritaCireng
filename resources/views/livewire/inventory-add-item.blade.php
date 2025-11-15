<div>
    <div class="px-6 py-5  bg-white rounded-xl m-4 shadow-sm">
        <h1 class="font-semibold ml-4 mb-4 text-dark">Tambah Barang</h1>

        <form wire:submit.prevent="save">

            {{-- NAMA --}}
            <input type="text" wire:model="name" placeholder="Nama Barang" class="w-full px-3 py-2 rounded-lg border border-primary-50 focus:border-primary-200 outline-none">
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
                @error('type') 
                    <p class="text-red-500 text-sm">{{ $message }}</p> 
                @enderror
            </div>

            {{-- HARGA --}}    
            <input type="number" wire:model="cost" placeholder="Harga Satuan" min="0" class="w-full px-3 py-2  mt-2 rounded-lg border border-primary-50 focus:border-primary-200 outline-none">
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
                @error('unit') 
                    <p class="text-red-500 text-sm">{{ $message }}</p> 
                @enderror
            </div>

            <div class="mt-4 flex gap-2 flex-end align-self-stretch">
                <button type="button" class="w-full bg-gray-200 border-1 border-gray-400 text-dark py-2 rounded-lg hover:bg-gray-300" onclick="window.location='{{ url("/inventory") }}'">
                    Batal
                </button>
                <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg">
                    Tambahkan
                </button>
            </div>
        </form>

    </div>
</div>
