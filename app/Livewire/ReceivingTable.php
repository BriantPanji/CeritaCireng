<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Delivery;
use App\Models\Outlet;
use Carbon\Carbon;

class ReceivingTable extends Component
{
    use WithPagination;

    public $page = 1;          // untuk pending deliveries
    public $historyPage = 1;   // untuk history deliveries

    public $filter_range = 'today';
    public $filter_status = '';
    public $filter_outlet = '';

    public $showDetail = false;
    public $selectedDeliveryId = null;
    public $selectedDelivery = null;

    public $outlets;

    

    protected $listeners = [
        'refreshComponent' => '$refresh'
    ];

    public function mount()
    {
        $this->outlets = Outlet::all();
    }

    public function updatedFilterRange()
    {
        $this->page = 1;
        $this->historyPage = 1;
    }

    public function updatedFilterStatus()
    {
        $this->page = 1;
        $this->historyPage = 1;
    }

    public function updatedFilterOutlet()
    {
        $this->page = 1;
        $this->historyPage = 1;
    }

    public function showDetail($id)
    {
        $this->selectedDeliveryId = $id;
        $this->selectedDelivery = Delivery::with('kurir', 'items')->find($id);
        $this->showDetail = true;
    }

    public function closeDetail()
    {
        $this->showDetail = false;
        $this->selectedDeliveryId = null;
        $this->selectedDelivery = null;
    }

    protected function getDateRange()
{
    $end = now(); // sekarang
    switch($this->filter_range) {
        case 'today':
            $start = now()->startOfDay();
            break;
        case 'week':
            $start = now()->subDays(7)->startOfDay();
            break;
        case 'month':
            $start = now()->subMonth()->startOfDay();
            break;
        case 'year':
            $start = now()->subYear()->startOfDay();
            break;
        case 'all':
        default:
            $start = null;
    }

    return $start ? [$start, $end] : null;
}


    public function render()
    {
        // Filter tanggal
        $startDate = match ($this->filter_range) {
            'today' => Carbon::today(),
            'week'  => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            'year'  => Carbon::now()->subYear(),
            default => null
        };

     $range = $this->getDateRange();

$pendingDeliveries = Delivery::with('kurir', 'items', 'outlet')
    ->when($range, function($q) use ($range) {
        [$start, $end] = $range;
        $q->whereBetween('assigned_at', [$start, $end]);
    })
    ->when($this->filter_status, fn($q) => $q->where('status', $this->filter_status))
    ->when($this->filter_outlet, fn($q) => $q->where('id_outlet', $this->filter_outlet))
    ->whereIn('status', ['DITUGASKAN', 'DIKIRIM'])
    ->paginate(10, ['*'], 'page', $this->page);

$historyDeliveries = Delivery::with('kurir', 'items', 'outlet')
    ->when($range, function($q) use ($range) {
        [$start, $end] = $range;
        $q->whereBetween('delivered_at', [$start, $end]);
    })
    ->when($this->filter_status, fn($q) => $q->where('status', $this->filter_status))
    ->when($this->filter_outlet, fn($q) => $q->where('id_outlet', $this->filter_outlet))
    ->whereIn('status', ['SELESAI', 'DIBATALKAN'])
    ->paginate(10, ['*'], 'historyPage', $this->historyPage);


        // Summary
        $summary = [
            'final' => $pendingDeliveries->sum(fn($d) => $d->items->sum('quantity')),
        ];

        return view('livewire.receiving-table', [
            'pendingDeliveries' => $pendingDeliveries,
            'historyDeliveries' => $historyDeliveries,
            'summary' => $summary,
        ]);
    }
}
