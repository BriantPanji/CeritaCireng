<x-layouts.app title="Dashboard Staff">
    <div class="p-4">
        <div
            class="{{ !auth()->user()->isTodayAttendance() || auth()->user()->isTodayAttendance() === 'ABSEN' ? 'bg-secondary' : 'hidden' }} p-3 rounded-lg mb-4 text-white flex items-center justify-between">
            <div>
                <strong>Absensi Hari Ini:</strong> Belum Absen
            </div>

            <a href="/absensi" class="bg-primary text-white px-4 py-2 rounded-lg font-semibold hover:opacity-90">
                Absen Sekarang
            </a>
        </div>

        {{-- Tabel pengantaran hari ini --}}
        <div class="mt-4">
            <h3 class="font-bold mb-3">Pengantaran Stok hari ini</h3>
            <livewire:pengantaran-table />
        </div>

        {{-- end of tabel pengantaran hari ini --}}

        {{-- Perbandingan Penerimaan vs Penjualan Barang --}}
        <div class="mt-12">
            <h3 class="font-bold mb-3">Perbandingan Penerimaan dan Penjualan Barang</h3>
            <livewire:product-comparison-chart />
        </div>
    </div>
</x-layouts.app>