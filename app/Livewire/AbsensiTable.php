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
    public $filter_range = 'all';
    public $filter_status = '';
    public $filter_role = '';
    public $editId;
    public $edit_date;
    public $edit_time;
    public $edit_status;

    protected $paginationTheme = 'tailwind';

    // Reset page setiap kali filter berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFilterRange()
    {
        $this->resetPage();
    }
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
    public function updatingFilterRole()
    {
        $this->resetPage();
    }

    public function getPagesProperty()
    {
        $paginator = $this->attendances;
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        $show = 3;

        if ($lastPage <= $show) {
            return range(1, $lastPage);
        }

        $start = $currentPage - 1;
        $end = $currentPage + 1;

        if ($start < 1) {
            $start = 1;
            $end = $show; // 3
        }

        if ($end > $lastPage) {
            $end = $lastPage;
            $start = $lastPage - ($show - 1);
        }

        return range($start, $end);
    }

    public function getAttendancesProperty()
    {
        $query = Attendance::with(['user.role']);

        // ==================
        // 🔎 SEARCH BY NAME
        // ==================
        if (!empty($this->search)) {
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
                // tidak ada filter
                break;

            default:
                // fallback aman → anggap today
                $query->whereDate('attendance_date', Carbon::today());
                break;
        }

        // ==================
        // 🟢 FILTER STATUS
        // ==================
        if (!empty($this->filter_status)) {
            $query->where('status', $this->filter_status);
        }

        // ==================
        // 🟦 FILTER ROLE
        // ==================
        if (!empty($this->filter_role)) {
            $query->whereHas('user.role', function ($q) {
                $q->where('display_name', $this->filter_role);
            });
        }

        // ==================
        // 📌 ORDER & PAGINATION
        // ==================
        return $query->orderBy('attendance_date', 'desc')
            ->orderBy('attendance_time', 'desc')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.absensi-table', [
            'attendances' => $this->attendances, // ← dynamic dari magic property
            'pages' => $this->pages,
        ]);
    }

    public function openEditModal($id)
{
    $att = Attendance::find($id);

    if (!$att) return;

    $this->editId = $att->id;
    $this->edit_date = $att->attendance_date;
    $this->edit_time = $att->attendance_time;
    $this->edit_status = $att->status;

    $this->dispatch('open-edit-modal');
}

public function saveEdit()
{
    $this->validate([
        'edit_date' => 'required|date',
        'edit_time' => 'required',
        'edit_status' => 'required|in:HADIR,IZIN,SAKIT,ABSEN',
    ]);

    Attendance::where('id', $this->editId)->update([
        'attendance_date' => $this->edit_date,
        'attendance_time' => $this->edit_time,
        'status' => $this->edit_status,
    ]);

    $this->dispatch('close-edit-modal');
}

}
