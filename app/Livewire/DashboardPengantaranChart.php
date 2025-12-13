<?php

namespace App\Livewire;

use App\Models\Delivery;
use Carbon\Carbon;
use Livewire\Component;

class DashboardPengantaranChart extends Component
{
    /**
     * Get fresh delivery stats - always fresh data
     */
    private function getDeliveryStats()
    {
        $today = Carbon::today()->toDateString();

        return [
            'selesai' => Delivery::whereDate('assigned_at', $today)->where('status', 'SELESAI')->count(),
            'diantar' => Delivery::whereDate('assigned_at', $today)->where('status', 'DIKIRIM')->count(),
            'ditugaskan' => Delivery::whereDate('assigned_at', $today)->whereIn('status', ['DITUGASKAN'])->count(),
            'gagal' => Delivery::whereDate('assigned_at', $today)->whereIn('status', ['DIBATALKAN'])->count(),
        ];
    }

    public function render()
    {
        return view('livewire.dashboard-pengantaran-chart', [
            'stats' => $this->getDeliveryStats(),
        ]);
    }
}
