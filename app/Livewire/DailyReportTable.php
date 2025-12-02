<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DailyOutletReport;
use App\Models\Outlet;
use Illuminate\Support\Facades\Auth;

class DailyReportTable extends Component
{
    use WithPagination;

    public $filterStatus = 'all'; // all, validated, invalidated
    public $filterDateFrom;
    public $filterDateTo;
    public $filterOutlet; // Admin bisa filter by outlet

    protected $queryString = [
        'filterStatus' => ['except' => 'all'],
        'filterDateFrom',
        'filterDateTo',
        'filterOutlet',
    ];

    public function render()
    {
        // Middleware handles authorization (checkrole:admin,dev)

        $query = DailyOutletReport::query()
            ->with(['staff', 'outlet', 'items'])
            ->latest('report_date')
            ->latest('report_time');

        // Filter by outlet (admin bisa lihat semua outlet)
        if ($this->filterOutlet) {
            $query->where('id_outlet', $this->filterOutlet);
        }

        if ($this->filterStatus === 'validated') {
            $query->validated();
        } elseif ($this->filterStatus === 'invalidated') {
            $query->invalidated();
        }

        if ($this->filterDateFrom) {
            $query->whereDate('report_date', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->whereDate('report_date', '<=', $this->filterDateTo);
        }

        $outlets = Outlet::all(); // For filter dropdown

        return view('livewire.daily-report-table', [
            'reports' => $query->paginate(20),
            'outlets' => $outlets,
        ]);
    }

    /**
     * Export to Excel
     */
    public function export()
    {
        return redirect()->route('daily-reports.export', [
            'status' => $this->filterStatus,
            'date_from' => $this->filterDateFrom,
            'date_to' => $this->filterDateTo,
            'outlet' => $this->filterOutlet,
        ]);
    }
}
