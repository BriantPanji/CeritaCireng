<x-layouts.app title="Dashboard">
    <div class="p-3 bg-neutral-25">

    <div class="p-4 min-h-screen">
        <div class="flex items-center bg-white rounded-full px-3 py-2 shadow-sm">
            <i class="fa-regular fa-magnifying-glass text-gray-400"></i>
            <input type="text" placeholder="Cari barang"
                class="flex-1 ml-2 outline-none text-sm text-gray-700 bg-transparent" />
            <button class="bg-primary text-white text-sm font-semibold px-4 py-1 rounded-full">
                Temukan
            </button>
        </div>

        <div class="mt-4 space-y-4">
            <div class="bg-white rounded-lg shadow-2xl overflow-hidden">
                <img src="{{ asset('images/Conan.jpg') }}" alt="Mangkuk Kertas" class="w-full h-44 object-cover">
                <div class="p-3">
                    <h2 class="font-semibold text-base">Mangkuk Kertas</h2>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-sm text-gray-500">Jumlah 1.250 Pcs</p>
                        <a href="#" class="text-primary text-sm font-semibold">Detail</a>
                    </div>
                </div>
            </div>


            <div class="bg-white rounded-lg shadow-2xl overflow-hidden">
                <img src="{{ asset('images/Conan.jpg') }}" alt="Sambal Cireng" class="w-full h-44 object-cover">
                <div class="p-3">
                    <h2 class="font-semibold text-base">Sambal Cireng</h2>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-sm text-gray-500">Jumlah 1.250 Pcs</p>
                        <a href="#" class="text-primary text-sm font-semibold">Detail</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
