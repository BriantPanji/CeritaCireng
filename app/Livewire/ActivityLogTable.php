<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\InventoryChangeLog;
use App\Models\DeliveryStatusAudit;
use App\Models\ItemChangeLog;
use App\Models\DailyReportLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActivityLogTable extends Component
{
    use WithPagination;

    public $search = '';
    public $logType = 'all'; // all, inventory, delivery, item, daily_report
    public $dateRange = 'today'; // today, week, month, year, all
    public $actionFilter = ''; // For filtering specific actions

    protected $paginationTheme = 'tailwind';

    // Reset pagination when filters change
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLogType()
    {
        $this->resetPage();
    }

    public function updatingDateRange()
    {
        $this->resetPage();
    }

    public function updatingActionFilter()
    {
        $this->resetPage();
    }

    /**
     * Get consolidated logs from all log tables
     */
    public function getLogsProperty()
    {
        $logs = collect();

        // Get date range filter
        $dateFilter = $this->getDateFilter();

        // Inventory Change Logs
        if (in_array($this->logType, ['all', 'inventory'])) {
            $inventoryLogs = InventoryChangeLog::with(['item' => function($query) {
                    $query->withTrashed();
                }])
                ->when($dateFilter, fn($q) => $q->where('timestamp', '>=', $dateFilter))
                ->when($this->search, function ($q) {
                    $q->whereHas('item', function ($query) {
                        $query->withTrashed()->where('name', 'like', '%' . $this->search . '%');
                    });
                })
                ->get()
                ->map(function ($log) {
                    $itemName = $log->item ? $log->item->name : 'Item Deleted';
                    return [
                        'id' => 'inv_' . $log->id,
                        'type' => 'inventory',
                        'type_label' => 'Inventory',
                        'action' => $log->change_amount > 0 ? 'Stock Bertambah' : 'Stock Berkurang',
                        'description' => "{$itemName}: {$log->old_stock} → {$log->new_stock} ({$log->formatted_change})",
                        'timestamp' => $log->timestamp,
                        'color' => 'blue',
                        'icon' => 'ph-package'
                    ];
                });
            $logs = $logs->merge($inventoryLogs);
        }

        // Delivery Status Logs
        if (in_array($this->logType, ['all', 'delivery'])) {
            $deliveryLogs = DeliveryStatusAudit::with('delivery.outlet', 'delivery.kurir')
                ->when($dateFilter, fn($q) => $q->where('timestamp', '>=', $dateFilter))
                ->when($this->search, function ($q) {
                    $q->whereHas('delivery', function ($query) {
                        $query->whereHas('outlet', function ($q2) {
                            $q2->where('name', 'like', '%' . $this->search . '%');
                        })->orWhereHas('kurir', function ($q2) {
                            $q2->where('display_name', 'like', '%' . $this->search . '%');
                        });
                    });
                })
                ->get()
                ->map(function ($log) {
                    $outletName = $log->delivery && $log->delivery->outlet ? $log->delivery->outlet->name : 'Outlet Deleted';
                    $kurirName = $log->delivery && $log->delivery->kurir ? $log->delivery->kurir->display_name : 'Kurir Deleted';
                    return [
                        'id' => 'del_' . $log->id,
                        'type' => 'delivery',
                        'type_label' => 'Pengantaran',
                        'action' => "Status: {$log->old_status} → {$log->new_status}",
                        'description' => "Pengantaran ke {$outletName} oleh {$kurirName}",
                        'timestamp' => $log->timestamp,
                        'color' => 'green',
                        'icon' => 'ph-truck'
                    ];
                });
            $logs = $logs->merge($deliveryLogs);
        }

        // Item Change Logs
        if (in_array($this->logType, ['all', 'item'])) {
            $itemLogs = ItemChangeLog::with(['item' => function($query) {
                    $query->withTrashed();
                }])
                ->when($dateFilter, fn($q) => $q->where('timestamp', '>=', $dateFilter))
                ->when($this->actionFilter, fn($q) => $q->where('action', $this->actionFilter))
                ->when($this->search, function ($q) {
                    $q->whereHas('item', function ($query) {
                        $query->withTrashed()->where('name', 'like', '%' . $this->search . '%');
                    });
                })
                ->get()
                ->map(function ($log) {
                    $itemName = $log->item ? $log->item->name : 'Item Deleted';
                    $description = match($log->action) {
                        'CREATE' => "Item baru: {$itemName}",
                        'UPDATE' => "{$itemName} - {$log->field_changed}: {$log->old_value} → {$log->new_value}",
                        'DELETE' => "Item dihapus: {$itemName}",
                        'RESTORE' => "Item dipulihkan: {$itemName}",
                        default => "{$itemName}"
                    };

                    return [
                        'id' => 'item_' . $log->id,
                        'type' => 'item',
                        'type_label' => 'Item Master',
                        'action' => $log->action,
                        'description' => $description,
                        'timestamp' => $log->timestamp,
                        'color' => 'purple',
                        'icon' => 'ph-cube'
                    ];
                });
            $logs = $logs->merge($itemLogs);
        }

        // Daily Report Logs
        if (in_array($this->logType, ['all', 'daily_report'])) {
            $reportLogs = DailyReportLog::with('report.outlet', 'report.staff')
                ->when($dateFilter, fn($q) => $q->where('timestamp', '>=', $dateFilter))
                ->when($this->actionFilter, fn($q) => $q->where('action', $this->actionFilter))
                ->when($this->search, function ($q) {
                    $q->whereHas('report', function ($query) {
                        $query->whereHas('outlet', function ($q2) {
                            $q2->where('name', 'like', '%' . $this->search . '%');
                        });
                    });
                })
                ->get()
                ->map(function ($log) {
                    $outletName = $log->report && $log->report->outlet ? $log->report->outlet->name : 'Outlet Deleted';
                    $reportDate = $log->report ? Carbon::parse($log->report->report_date)->format('d M Y') : 'N/A';
                    return [
                        'id' => 'report_' . $log->id,
                        'type' => 'daily_report',
                        'type_label' => 'Laporan Harian',
                        'action' => $log->action,
                        'description' => "Laporan {$outletName} - {$reportDate}",
                        'timestamp' => $log->timestamp,
                        'color' => 'orange',
                        'icon' => 'ph-file-text'
                    ];
                });
            $logs = $logs->merge($reportLogs);
        }

        // Sort by timestamp descending and paginate
        return $logs->sortByDesc('timestamp')->values();
    }

    /**
     * Get date filter based on selected range
     */
    private function getDateFilter()
    {
        return match($this->dateRange) {
            'today' => Carbon::today(),
            'week' => Carbon::now()->subDays(7),
            'month' => Carbon::now()->subDays(30),
            'year' => Carbon::now()->subYear(),
            'all' => null,
            default => Carbon::today()
        };
    }

    /**
     * Custom pagination
     */
    public function getPagesProperty()
    {
        $total = $this->logs->count();
        $perPage = 10;
        $currentPage = $this->getPage();
        $lastPage = (int) ceil($total / $perPage);

        $show = 3;

        if ($lastPage <= $show) {
            return range(1, $lastPage);
        }

        $start = $currentPage - 1;
        $end = $currentPage + 1;

        if ($start < 1) {
            $start = 1;
            $end = $show;
        }

        if ($end > $lastPage) {
            $end = $lastPage;
            $start = $lastPage - ($show - 1);
        }

        return range($start, $end);
    }

    /**
     * Get paginated logs
     */
    public function getPaginatedLogsProperty()
    {
        $perPage = 10;
        $currentPage = $this->getPage();
        
        return $this->logs->forPage($currentPage, $perPage);
    }

    public function render()
    {
        return view('livewire.activity-log-table', [
            'logs' => $this->paginatedLogs,
            'pages' => $this->pages,
            'total' => $this->logs->count()
        ]);
    }
}
