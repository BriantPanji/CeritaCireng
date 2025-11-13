<x-layouts.app title="Dashboard">
    <div class="p-3">

        {{-- Presensi --}}
        <div class="shadow-reguler px-2 py-3 rounded-md">
            <h1 class="text-1">Presensi</h1>
            <p class="text-reguler font-semibold">Anda belum absen hari ini</p>
            <button class="bg-primary text-white p-2 mt-2 rounded-lg">Absen Sekarang</button>
        </div>
        {{-- End of presensi --}}

        {{-- Inventaris --}}
        <div class="mt-4">
            <div>
                <h1 class="border-b-2 border-b-primary-50 w-fit font-medium">Inventaris</h1>
                <div class="px-6 py-6 mt-2 shadow-reguler flex gap-5 items-center rounded-lg">
                    <i class="fa-light fa-box text-l1 bg-primary-50 rounded-full  w-14 h-14 text-center  p-4"></i>
                    <div>
                        <h2 class="text-reguler font-medium">Stok Gudang</h2>
                        <i class="ph ph-warehouse"></i>
                        <p class="text-h3 font-semibold">11.500</p>
                    </div>
                </div>
            </div>
            <div>
                <div class="px-6 py-6 mt-2 shadow-reguler flex gap-5 items-center rounded-lg">
                    <i
                        class="fa-light fa-wine-glass-crack text-l1 bg-primary-50 rounded-full w-14 h-14 text-center p-4"></i>
                    <div>
                        <h2 class="text-reguler font-medium">Barang Rusak</h2>
                        <p class="text-h3 font-semibold">541</p>
                    </div>
                </div>
            </div>
        </div>
        {{-- End of Inventaris --}}

        {{-- Pengiriman --}}
        <div class="mt-4">
            <div>
                <h1 class="border-b-2 border-b-primary-50 w-fit font-medium">Pengiriman</h1>
                <div class="px-6 py-6 mt-2 shadow-reguler flex gap-5 items-center rounded-lg">
                    <i
                        class="fa-light fa-truck-ramp-box text-[1.3rem] bg-primary-50 rounded-full w-14 h-14 text-center  p-4"></i>
                    <div>
                        <h2 class="text-reguler font-medium">Total Pengantaran</h2>
                        <p class="text-h3 font-semibold">12.000</p>
                    </div>
                </div>
            </div>
            <div>
                <div class="px-6 py-6 mt-2 shadow-reguler flex gap-5 items-center rounded-lg">
                    <i
                        class="fa-light fa-user-plus text-l2 bg-primary-50 rounded-full w-[53px] h-[53px] text-center p-4"></i>
                    <div>
                        <h2 class="text-1 font-light"><span class="font-medium">Top Kurir</span>/Total
                            Pengiriman</h2>
                        <p class="text-l1 font-semibold">John Doe<span class="text-l2 font-normal">/1.250 kali</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        {{-- End of Pengiriman --}}

        {{-- Outlet --}}
        <div class="mt-4">
            <div>
                <h1 class="border-b-2 border-b-primary-50 w-fit font-medium">Outlet</h1>
                <div class="px-6 py-6 mt-2 shadow-reguler flex gap-5 items-center rounded-lg">
                    <i
                        class="fa-light fa-shop text-[1.25rem] bg-primary-50 rounded-full w-[56px] h-[56px] text-center p-4"></i>
                    <div>
                        <h2 class="text-reguler font-medium">Total Outlet</h2>
                        <p class="text-h3 font-semibold">10</p>
                    </div>
                </div>
            </div>
            <div>
                <div class="px-6 py-6 mt-2 shadow-reguler flex gap-5 items-center rounded-lg">
                    <i
                        class="fa-light fa-cart-shopping text-l2 bg-primary-50 rounded-full w-[54px] h-[54px] text-center p-4"></i>
                    <div>
                        <h2 class="text-1 font-medium">Barang Terjual</h2>
                        <p class="text-l1 font-semibold">150.300</span>
                        </p>
                    </div>
                </div>
            </div>
            <div>
                <div class="px-6 py-6 mt-2 shadow-reguler flex gap-5 items-center rounded-lg">
                    <i
                        class="fa-light fa-wallet text-l2 bg-primary-50 rounded-full w-[53px] h-[53px] text-center p-4"></i>
                    <div>
                        <h2 class="text-1 font-medium">Pendapatan Bulanan</h2>
                        <p class="text-l1 font-semibold">Rp390.420.000</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        {{-- End of Outlet --}}

        {{-- Grafik --}}
        <div class="mt-4">
            <div>
                <h1 class="border-b-2 border-b-primary-50 w-fit font-medium">Grafik</h1>
                <div class="px-6 py-6 mt-2 shadow-reguler flex gap-5 items-center rounded-lg">
                    <i
                        class="fa-light fa-shop text-[1.25rem] bg-primary-50 rounded-full w-[56px] h-[56px] text-center p-4"></i>
                    <div>
                        <h2 class="text-reguler font-medium">Total Outlet</h2>
                        <p class="text-h3 font-semibold">10</p>
                    </div>
                </div>
            </div>
        </div>
        {{-- End of Grafik --}}
    </div>
</x-layouts.app>
