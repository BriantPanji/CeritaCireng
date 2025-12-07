<x-layouts.app title="Laporkan Penerimaan">

    <div class="p-4">

        <h2 class="font-bold text-xl mb-4">Laporan Kesalahan Barang</h2>

        <form method="POST" enctype="multipart/form-data">
            @csrf

            <label class="block mb-2 font-semibold">Barang yang Salah</label>
            <select name="item_id" class="w-full border rounded p-2 mb-4">
                @foreach ($return->returnItem as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>

            <label class="block mb-2 font-semibold">Jumlah Salah</label>
            <input type="number" name="wrong_quantity" class="w-full border p-2 rounded mb-4" min="1">

            <label class="block mb-2 font-semibold">Alasan (opsional)</label>
            <textarea name="reason" class="w-full border rounded p-2 mb-4"></textarea>

            <label class="block mb-2 font-semibold">Foto Bukti</label>
            <input type="file" name="photo" class="mb-4" required>

            <button class="bg-red-600 text-white px-4 py-2 rounded">Laporkan</button>
        </form>

    </div>
</x-layouts.app>
