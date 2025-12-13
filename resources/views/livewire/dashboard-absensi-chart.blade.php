<div>
    <canvas 
        x-data="{ chart: null }"
        x-init="
            const ctx = $el.getContext('2d');
            
            chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Izin', 'Sakit', 'Absen'],
                    datasets: [{
                        data: [@js($stats['hadir']), @js($stats['izin']), @js($stats['sakit']), @js($stats['absen'])],
                        backgroundColor: ['#00c951', '#CD9100', '#FF3704', '#A09C97'],
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '60%',
                }
            });
        "
        wire:key="absensi-chart-{{ now()->timestamp }}"
        id="absensiChart">
    </canvas>
</div>
