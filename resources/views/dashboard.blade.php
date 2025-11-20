<x-layouts.app title="Dashboard">
    <div class="p-3">
        <div class="flex flex-wrap md:justify-center max-w-[1200px] mx-auto">
            {{-- Inventaris --}}
            <div class="mt-4 w-full lg:w-fit">
                <h1 class="border-b-2 border-b-primary-50 w-fit font-medium">Inventaris</h1>
                <div class="md:flex gap-9">
                    <div class="px-6 py-6 mt-2 shadow-reguler flex gap-5 items-center rounded-lg w-full lg:w-[500px]">
                        <i class="fa-light fa-box text-l1 bg-primary-50 rounded-full  w-14 h-14 text-center  p-4"></i>
                        <div>
                            <h2 class="text-reguler font-medium">Stok Gudang</h2>
                            <p class="text-h3 font-semibold">11.500</p>
                        </div>
                    </div>
                    <div class="px-6 py-6 mt-2 shadow-reguler flex gap-5 items-center rounded-lg w-full lg:w-[500px]">
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
            <div class="mt-4 w-full lg:w-fit">
                <h1 class="border-b-2 border-b-primary-50 w-fit font-medium">Pengiriman</h1>
                <div class="md:flex gap-9">
                    <div class="px-6 py-6 mt-2 shadow-reguler flex gap-5 items-center rounded-lg w-full lg:w-[500px]">
                        <i
                            class="fa-light fa-truck-ramp-box text-l1 bg-primary-50 rounded-full  w-14 h-14 text-center  p-4"></i>
                        <div>
                            <h2 class="text-reguler font-medium">Total Pengantaran</h2>
                            <p class="text-h3 font-semibold">12.000</p>
                        </div>
                    </div>
                    <div class="px-6 py-6 mt-2 shadow-reguler flex gap-5 items-center rounded-lg w-full lg:w-[500px]">
                        <i
                            class="fa-light fa-user-plus text-l1 bg-primary-50 rounded-full w-14 h-14 text-center p-4"></i>
                        <div>
                            <h2 class="text-1 font-light"><span class="font-medium">Top Kurir</span>/Total
                                Pengiriman</h2>
                            <p class="text-l1 font-semibold">John Doe<span class="text-l2 font-normal">/1.250
                                    kali</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- End of Pengiriman --}}

            {{-- Outlet --}}
            <div class="mt-4 w-full lg:w-fit">
                <h1 class="border-b-2 border-b-primary-50 w-fit font-medium">Outlet</h1>
                <div class="md:flex gap-9">
                    <div class="px-6 py-6 mt-2 shadow-reguler flex gap-5 items-center rounded-lg w-full lg:w-[500px]">
                        <i
                            class="fa-light fa-shop text-[1.25rem] bg-primary-50 rounded-full w-[56px] h-[56px] text-center p-4"></i>
                        <div>
                            <h2 class="text-reguler font-medium">Total Outlet</h2>
                            <p class="text-h3 font-semibold">{{ $totalOutlet }}</p>
                        </div>
                    </div>
                    <div class="px-6 py-6 mt-2 shadow-reguler flex gap-5 items-center rounded-lg w-full lg:w-[500px]">
                        <i
                            class="fa-light fa-cart-shopping text-l2 bg-primary-50 rounded-full w-[54px] h-[54px] text-center p-4"></i>
                        <div>
                            <h2 class="text-1 font-medium">Barang Terjual</h2>
                            <p class="text-l1 font-semibold">150.300</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- End of Outlet --}}
        </div>

        {{-- Grafik --}}
        <div class="mt-12 lg:w-[500px] flex flex-wrap justify-center mx-auto">
            <canvas id="myChart">
            </canvas>
        </div>
        {{-- End of Grafik --}}
    </div>
</x-layouts.app>

<script>
    const ctx = document.getElementById('myChart').getContext('2d');

    // Buat gradient merah (atas -> bawah)
    const redGradient = ctx.createLinearGradient(0, 0, 0, 400);
    redGradient.addColorStop(0, 'rgba(255, 99, 132, 0.6)');
    redGradient.addColorStop(1, 'rgba(255, 99, 132, 0.05)');

    // Buat gradient biru (atas -> bawah)
    const blueGradient = ctx.createLinearGradient(0, 0, 0, 400);
    blueGradient.addColorStop(0, 'rgba(54, 162, 235, 0.6)');
    blueGradient.addColorStop(1, 'rgba(54, 162, 235, 0.05)');

    const labels = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'];

    const data = {
        labels: labels,
        datasets: [{
                label: 'Barang rusak',
                data: [120, 150, 180, 160],
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: blueGradient,
                tension: 0.3,
                fill: true
            },
            {
                label: 'Barang Terjual',
                data: [200, 340, 650, 550],
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: redGradient,
                tension: 0.3,
                fill: true
            }
        ]
    };

    const config = {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Grafik Arus Barang'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                }
            }
        }
    };

    new Chart(ctx, config);
</script>
