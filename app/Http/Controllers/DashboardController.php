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
        $dayNumber = now()->dayOfWeekIso; // 1=Senin, 7=Minggu

        // Ambil semua user yang aktif dengan role inventaris, kurir, atau staff
        $users = User::with(['outlet', 'role'])
            ->where('status', 'AKTIF')
            ->whereHas('role', function ($query) {
                $query->whereIn('name', ['inventaris', 'kurir', 'staff']);
            })
            ->get();

        // Buat absensi untuk setiap user jika belum ada
        foreach ($users as $user) {
            // Skip jika user tidak punya outlet
            if (!$user->outlet) {
                continue;
            }
            
            // Skip kalau user punya outlet statusnya nonaktif
            if ($user->outlet->status === 'NONAKTIF') {
                continue;
            }

            // Cek apakah outlet tutup di hari ini
            $isOutletClosed = $user->outlet->isClosedOn($dayNumber);

            // Jika outlet tutup, skip pembuatan absensi
            if ($isOutletClosed) {
                continue;
            }

            // Buat absensi jika outlet buka
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

        // Ambil role user yang login
        $userRole = Auth::user()->role->name ?? null;

        // Return view berbeda berdasarkan role
        switch ($userRole) {
            case 'kurir':
                return $this->kurirDashboard();
            
            case 'staff':
                return redirect()->route('staff.dashboard');
            
            case 'admin':
            case 'dev':
            default:
                return $this->adminDashboard();
        }
    }

    /**
     * Dashboard untuk Admin dan Dev (Full Access)
     */
    private function adminDashboard()
    {
        $today = now()->toDateString();
        
        // Mengambil outlet dari user yang sedang login
        $user = Auth::user();
        $outlets = Outlet::all();

        // Outlet
        $totalOutlet = Outlet::count();

        // Mengambil data pengantaran
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

    /**
     * Mulai pengiriman (Update status ke DIKIRIM)
     */
    public function startDelivery($id)
    {
        $delivery = Delivery::findOrFail($id);
        
        // Pastikan yang akses adalah kurir yang ditugaskan
        if ($delivery->id_kurir !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($delivery->status === 'DITUGASKAN') {
            $delivery->update([
                'status' => 'DIKIRIM'
            ]);
        }

        return redirect()->back()->with('success', 'Status pengiriman berhasil diperbarui!');
    }

    /**
     * Dashboard untuk Kurir
     */
    private function kurirDashboard()
    {
        $today = now()->toDateString();
        $user = Auth::user();

        // Ambil semua delivery untuk kurir ini
        $myDeliveries = Delivery::with(['outlet', 'inventaris', 'items.item'])
            ->where('id_kurir', $user->id)
            ->orderBy('assigned_at', 'desc')
            ->get();

        // Delivery hari ini
        $todayDeliveries = $myDeliveries->filter(function ($delivery) use ($today) {
            return \Carbon\Carbon::parse($delivery->assigned_at)->toDateString() === $today;
        });

        // Statistik delivery hari ini
        $selesai = $todayDeliveries->where('status', 'SELESAI')->count();
        $dikirim = $todayDeliveries->where('status', 'DIKIRIM')->count();
        $ditugaskan = $todayDeliveries->where('status', 'DITUGASKAN')->count();
        $dibatalkan = $todayDeliveries->where('status', 'DIBATALKAN')->count();

        // Delivery yang masih aktif (belum selesai)
        $activeDeliveries = $myDeliveries->whereIn('status', ['DITUGASKAN', 'DIKIRIM']);

        // Total delivery sepanjang waktu
        $totalDeliveries = $myDeliveries->count();
        $totalSelesai = $myDeliveries->where('status', 'SELESAI')->count();

        return view('kurir.dashboard', [
            'todayDeliveries' => $todayDeliveries,
            'activeDeliveries' => $activeDeliveries,
            'selesai' => $selesai,
            'dikirim' => $dikirim,
            'ditugaskan' => $ditugaskan,
            'dibatalkan' => $dibatalkan,
            'totalDeliveries' => $totalDeliveries,
            'totalSelesai' => $totalSelesai,
        ]);
    }
}
