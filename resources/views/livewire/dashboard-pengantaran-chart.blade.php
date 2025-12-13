<div>
    <canvas 
        x-data="{ chart: null }"
        x-init="
            const ctx = $el.getContext('2d');
            
            chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Selesai', 'Diantar', 'Ditugaskan', 'Dibatalkan/Terkendala'],
                    datasets: [{
                        data: [@js($stats['selesai']), @js($stats['diantar']), @js($stats['ditugaskan']), @js($stats['gagal'])],
                        backgroundColor: ['#00c951', '#A09C97', '#CD9100', '#FF3704'],
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '60%',
                }
            });
        "
        wire:key="pengantaran-chart-{{ now()->timestamp }}"
        id="pengantaranChart">
    </canvas>
</div>
