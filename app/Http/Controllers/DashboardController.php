<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Outlet;
use App\Models\Delivery;
use App\Models\Inventory;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $users = User::all();

        foreach ($users as $user) {
            Attendance::firstOrCreate(
                [
                    'id_user' => $user->id,
                    'attendance_date' => $today,
                ],
                [
                    'status' => 'ABSEN',
                ]
            );
        }
        // Mengambil outlet dari user yang sedang login
        $user = Auth::user();
        // $idOutlet = $user->outlet_id;
        $outlets = Outlet::all();

        // Outlet
        $totalOutlet = Outlet::count();

        // Mengambil data pengantaran
        $today = now()->toDateString();

        $selesai = Delivery::whereDate('assigned_at', $today)
            ->where('status', 'SELESAI')
            ->count();

        $diantar = Delivery::whereDate('assigned_at', $today)
            ->where('status', 'DIKIRIM')
            ->count();

        $ditugaskan = Delivery::whereDate('assigned_at', $today)
            ->whereIn('status', ['DITUGASKAN'])
            ->count();

        $gagal = Delivery::whereDate('assigned_at', $today)
            ->whereIn('status', ['DIBATALKAN'])
            ->count();

        // --- DATA ABSENSI ---
        $hadir = Attendance::whereDate('attendance_date', $today)
            ->where('status', 'HADIR')
            ->count();

        $izin = Attendance::whereDate('attendance_date', $today)
            ->where('status', 'IZIN')
            ->count();

        $sakit = Attendance::whereDate('attendance_date', $today)
            ->where('status', 'SAKIT')
            ->count();

        $absen = Attendance::whereDate('attendance_date', $today)
            ->where('status', 'ABSEN')
            ->count();

        // Mengirim data ke view
        return view('dashboard', [
            'outlets' => $outlets,
            'totalOutlet' => $totalOutlet,
            'inventories' => Inventory::orderBy('stock', 'asc')->get(),
            'selesai' => $selesai,
            'diantar' => $diantar,
            'ditugaskan' => $ditugaskan,
            'gagal' => $gagal,
            // data absensi
            'hadir' => $hadir,
            'izin' => $izin,
            'sakit' => $sakit,
            'absen' => $absen,
        ]);
    }
}
