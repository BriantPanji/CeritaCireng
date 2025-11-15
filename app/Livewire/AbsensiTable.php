<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Attendance;
use Carbon\Carbon;

class AbsensiTable extends Component
{
    use WithPagination;

    public $search = '';
    public $filter_range = 'today';
    public $filter_status = '';
    public $filter_role = '';

    protected $paginationTheme = 'tailwind';

    // Reset page setiap kali filter berubah
    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterRange() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterRole() { $this->resetPage(); }

    public function render()
    {
        $query = Attendance::with(['user.role']);

        // ==================
        // 🔎 SEARCH BY NAME
        // ==================
        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('display_name', 'like', '%' . $this->search . '%');
            });
        }

        // ==================
        // ⏳ FILTER WAKTU
        // ==================
        switch ($this->filter_range) {
            case 'today':
                $query->whereDate('attendance_date', Carbon::today());
                break;

            case 'week':
                $query->whereBetween('attendance_date', [
                    Carbon::now()->subDays(7),
                    Carbon::now()
                ]);
                break;

            case 'month':
                $query->whereBetween('attendance_date', [
                    Carbon::now()->subDays(30),
                    Carbon::now()
                ]);
                break;

            case 'year':
                $query->whereBetween('attendance_date', [
                    Carbon::now()->subYear(),
                    Carbon::now()
                ]);
                break;

            case 'all':
            default:
                // Tidak ada filter waktu
                break;
        }

        // ==================
        // 🟢 FILTER STATUS
        // ==================
        if ($this->filter_status) {
            $query->where('status', $this->filter_status);
        }

        // ==================
        // 🟦 FILTER ROLE
        // ==================
        if ($this->filter_role) {
            $query->whereHas('user.role', function ($q) {
                $q->where('display_name', $this->filter_role);
            });
        }

        // ==================
        // 📌 ORDER & PAGINATION
        // ==================
        $attendances = $query
            ->orderBy('attendance_date', 'desc')
            ->orderBy('attendance_time', 'desc')
            ->paginate(10);

        return view('livewire.absensi-table', [
            'attendances' => $attendances,
        ]);
    }
}
