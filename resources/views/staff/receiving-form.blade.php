<x-layouts.app title="Konfirmasi Pengantaran">

    <div class="p-4">

        <h2 class="font-bold text-xl mb-4">Konfirmasi Barang Masuk</h2>

        <form method="POST">
            @csrf

            <label class="block mb-2 font-semibold">Catatan (opsional)</label>
            <textarea name="notes" class="w-full border rounded p-2 mb-4"></textarea>

            <button class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
        </form>

    </div>
</x-layouts.app>
