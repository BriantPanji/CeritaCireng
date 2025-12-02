<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userRole = $user->role->name ?? null;

        // Role inventaris, kurir, staff -> Lihat absensi sendiri aja
        if (in_array($userRole, ['inventaris', 'kurir', 'staff'])) {
            return $this->myAttendance();
        }

        // Role admin, dev, atau lainnya -> Lihat semua absensi
        return $this->allAttendance();
    }

    /**
     * Halaman absensi untuk user sendiri (inventaris, kurir, staff)
     */
    private function myAttendance()
    {
        // Return ke view my-attendance yang wrap Volt component
        return view('my-attendance');
    }

    /**
     * Halaman absensi untuk admin/dev (lihat semua)
     */
    private function allAttendance()
    {
        // Return ke absensi table (yang sudah ada)
        return view('absensi');
    }
}
