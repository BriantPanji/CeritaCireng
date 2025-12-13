<?php

namespace App\Livewire;

use App\Models\Attendance;
use Carbon\Carbon;
use Livewire\Component;

class DashboardAbsensiChart extends Component
{
    /**
     * Get fresh attendance stats - always fresh data
     */
    private function getAttendanceStats()
    {
        $today = Carbon::today()->toDateString();

        return [
            'hadir' => Attendance::whereDate('attendance_date', $today)->where('status', 'HADIR')->count(),
            'izin' => Attendance::whereDate('attendance_date', $today)->where('status', 'IZIN')->count(),
            'sakit' => Attendance::whereDate('attendance_date', $today)->where('status', 'SAKIT')->count(),
            'absen' => Attendance::whereDate('attendance_date', $today)->where('status', 'ABSEN')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.dashboard-absensi-chart', [
            'stats' => $this->getAttendanceStats(),
        ]);
    }
}
