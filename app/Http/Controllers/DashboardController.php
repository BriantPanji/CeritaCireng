<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Outlet;
use App\Models\Delivery;
use App\Models\Inventory;
use App\Models\Attendance;
use App\Models\Item;
use App\Models\ReturnModel;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $dayNumber = now()->dayOfWeekIso; // 1=Senin, 7=Minggu

        // Ambil semua user yang aktif dengan role kurir atau staff (exclude inventaris)
        $users = User::with(['outlet', 'role'])
            ->where('status', 'AKTIF')
            ->whereHas('role', function ($query) {
                $query->whereIn('name', ['kurir', 'staff']); // Only kurir & staff
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
                return $this->staffDashboard();

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
        // Mengambil outlet dari user yang sedang login
        $user = Auth::user();
        $outlets = Outlet::all();

        // Outlet
        $totalOutlet = Outlet::count();

        // NOTE: Chart data (pengantaran & absensi) now handled by Livewire components
        // No need to pass to view anymore

        // Mengirim data ke view
        return view('dashboard', [
            'outlets' => $outlets,
            'totalOutlet' => $totalOutlet,
            'inventories' => Inventory::orderBy('stock', 'asc')->get(),
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
                'status' => 'DIKIRIM',
                'delivered_at' => Carbon::now()->toDateTimeString(),
            ]);
        }

        return redirect()->back()->with('success', 'Status pengiriman berhasil diperbarui!');
    }

    /**
     * Dashboard untuk Staff
     */
    private function staffDashboard()
    {
        $staff = Auth::user();
        $outlet = $staff->outlet ?? null;

        // Stat Cards
        $barangTersedia = Item::count();
        $barangTerjual = 0;

        // Data pengembalian terbaru
        $penerimaanTerbaru = ReturnModel::with(['returnItem', 'staff'])
            ->orderBy('returned_at', 'desc')
            ->limit(5)
            ->get();

        $deliveries = Delivery::where('id_outlet', $staff->outlet_id)
            ->whereDate('assigned_at', Carbon::today())
            ->get();

        return view('staff.dashboard', compact(
            'deliveries',
            'staff',
            'outlet',
            'barangTersedia',
            'barangTerjual',
            'penerimaanTerbaru'
        ));
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
