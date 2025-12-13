<div>
    {{-- FILTER BUTTONS --}}
    <div class="flex gap-2 mb-4 flex-wrap">
        <button wire:click="setFilter('week')" 
            class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-300
                {{ $timeFilter === 'week' ? 'bg-primary text-white shadow-button' : 'bg-white text-neutral-500 hover:bg-neutral-50' }}">
            1 Minggu
        </button>
        <button wire:click="setFilter('month')" 
            class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-300
                {{ $timeFilter === 'month' ? 'bg-primary text-white shadow-button' : 'bg-white text-neutral-500 hover:bg-neutral-50' }}">
            1 Bulan
        </button>
        <button wire:click="setFilter('year')" 
            class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-300
                {{ $timeFilter === 'year' ? 'bg-primary text-white shadow-button' : 'bg-white text-neutral-500 hover:bg-neutral-50' }}">
            1 Tahun
        </button>
    </div>

    {{-- CHART CONTAINER --}}
    <div class="bg-white rounded-2xl shadow-md p-6" wire:key="chart-container-{{ $timeFilter }}">
        <canvas id="productComparisonChart-{{ $timeFilter }}" 
            x-data="{
                chart: null
            }"
            x-init="
                const ctx = $el.getContext('2d');
                
                // Create gradient for received line (RED)
                const receivedGradient = ctx.createLinearGradient(0, 0, 0, 400);
                receivedGradient.addColorStop(0, 'rgba(239, 68, 68, 0.4)');
                receivedGradient.addColorStop(1, 'rgba(239, 68, 68, 0.01)');
                
                // Create gradient for sold line (BLUE)
                const soldGradient = ctx.createLinearGradient(0, 0, 0, 400);
                soldGradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
                soldGradient.addColorStop(1, 'rgba(59, 130, 246, 0.01)');
                
                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @js($chartData['labels']),
                        datasets: [
                            {
                                label: 'Barang Diterima',
                                data: @js($chartData['received']),
                                borderColor: 'rgb(239, 68, 68)',
                                backgroundColor: receivedGradient,
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                pointBackgroundColor: 'rgb(239, 68, 68)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                            },
                            {
                                label: 'Barang Terjual',
                                data: @js($chartData['sold']),
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: soldGradient,
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                pointBackgroundColor: 'rgb(59, 130, 246)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        aspectRatio: 2,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    font: {
                                        size: 12,
                                        weight: '600'
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 12
                                },
                                bodySpacing: 4,
                                usePointStyle: true,
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.parsed.y + ' unit';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            "
            class="w-full"
            style="max-height: 400px;">
        </canvas>
    </div>
</div>
