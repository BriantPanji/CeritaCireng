<x-layouts.app title="Add Item">
    <form action="{{ route('item.store') }}" method="POST">
        @csrf    
    <div class="px-6 py-5 bg-white rounded-xl m-4 shadow-sm">
        <h1 class="font-semibold ml-4 mb-4 text-dark">Tambah Barang</h1>
    <div class="px-3 py-2 mt-2 rounded-lg border-1 border-primary-50">
        <input type="text" name="name" placeholder="Nama Barang">
    </div>
    
    <div class="px-3 py-2 mt-2 rounded-lg border-1 border-primary-50">
        {{-- kategori --}}
    </div>

    <div class="px-3 py-2 mt-2 rounded-lg border-1 border-primary-50">
        <input type="number" name="cost" placeholder="Harga Satuan">
    </div>
    
    <div class="px-3 py-2 mt-2 rounded-lg border-1 border-primary-50">
        {{-- satuan --}}
    </div>
    
    <div>
    </form>
</x-layouts.app>
