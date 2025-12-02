<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\DailyReportService;
use App\Models\DailyOutletReport;
use App\Models\Item;
use App\Models\Outlet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class DailyReportCreate extends Component
{
    public $reportDate;
    public $notes;
    public $items = []; // Array of items with manual input
    public $availableItems = [];
    public $selectedOutlet; // For admin/dev to select outlet
    public $outlets = []; // List of outlets for admin/dev

    protected $rules = [
        'reportDate' => 'required|date',
        'selectedOutlet' => 'required|exists:outlets,id',
        'notes' => 'nullable|string|max:1000',
        'items.*.initial_stock' => 'required|integer|min:0',
        'items.*.stock_remained' => 'required|integer|min:0',
        'items.*.qty_damaged' => 'required|integer|min:0',
        'items.*.qty_sold' => 'required|integer|min:0',
    ];

    public function mount()
    {
        $this->reportDate = today()->toDateString();
        $user = Auth::user();

        if (Gate::allows('checkrole', 'dev,admin')) {
            $this->outlets = Outlet::all();
            // If admin/dev already has outlet, select it by default
            $this->selectedOutlet = $user->outlet_id;
        } else {
            // Staff must have outlet assigned
            if (!$user->outlet_id) {
                session()->flash('error', 'Anda tidak memiliki outlet. Hanya staff dengan outlet yang dapat membuat laporan.');
                return redirect()->route('dashboard');
            }
            $this->selectedOutlet = $user->outlet_id;
        }

        // Load items after outlet is set
        $this->loadAvailableItems();
    }

    public function updatedSelectedOutlet()
    {
        // Load items when outlet is selected/changed
        $this->loadAvailableItems();
        $this->checkDuplicateReport();
    }

    public function checkDuplicateReport()
    {
        if (!$this->selectedOutlet) return;

        $existingReport = DailyOutletReport::where('id_outlet', $this->selectedOutlet)
            ->whereDate('report_date', $this->reportDate)
            ->first();

        if ($existingReport) {
            session()->flash('warning', 'Peringatan: Laporan untuk outlet ini di tanggal tersebut sudah ada!');
        } else {
            session()->forget('warning');
        }
    }

    public function loadAvailableItems()
    {
        if (!$this->selectedOutlet) {
            $this->availableItems = collect();
            $this->items = [];
            return;
        }

        $outletItems = DB::table('outlet_item_settings')
            ->where('id_outlet', $this->selectedOutlet)
            ->pluck('id_item')
            ->toArray();

        $this->availableItems = Item::whereIn('id', $outletItems)->get();

        // Initialize items array
        $this->items = [];
        foreach ($this->availableItems as $item) {
            $this->items[$item->id] = [
                'initial_stock' => 0,
                'stock_remained' => 0,
                'qty_damaged' => 0,
                'qty_sold' => 0,
            ];
        }
    }

    public function createReport(DailyReportService $service)
    {
        $this->validate();

        try {
            // Check again for duplicate
            $existing = DailyOutletReport::where('id_outlet', $this->selectedOutlet)
                ->whereDate('report_date', $this->reportDate)
                ->first();

            if ($existing) {
                throw ValidationException::withMessages([
                    'reportDate' => 'Laporan untuk outlet ini di tanggal tersebut sudah ada!'
                ]);
            }

            $report = $service->generateReport(
                outletId: $this->selectedOutlet,
                staffId: Auth::id(),
                reportDate: $this->reportDate,
                notes: $this->notes
            );

            // Update items with manual input
            foreach ($this->items as $itemId => $itemData) {
                $reportItem = $report->items()->where('id_item', $itemId)->first();
                if ($reportItem) {
                    $service->updateReportItem($reportItem->id, $itemData);
                }
            }

            session()->flash('success', 'Laporan berhasil dibuat!');

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membuat laporan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.daily-report-create');
    }
}
