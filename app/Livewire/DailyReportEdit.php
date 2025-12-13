<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DailyOutletReport;
use App\Services\DailyReportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app'), Title('Edit Laporan Harian')]
class DailyReportEdit extends Component
{
    public DailyOutletReport $report;
    public $notes;
    public $items = [];

    protected $rules = [
        'notes' => 'nullable|string|max:1000',
        'items.*.initial_stock' => 'required|integer|min:0',
        'items.*.stock_remained' => 'required|integer|min:0',
        'items.*.qty_damaged' => 'required|integer|min:0',
        'items.*.qty_sold' => 'required|integer|min:0',
    ];

    public function mount($id)
    {
        $this->report = DailyOutletReport::with('items')->findOrFail($id);

        // Authorization check: staff in same outlet OR admin/dev
        $user = Auth::user();
        $canEdit = Gate::allows('checkrole', 'dev,admin') ||
            ($user->outlet_id && $user->outlet_id === $this->report->id_outlet);

        if (!$canEdit) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit laporan ini.');
        }

        // Check if report is still valid (can only edit valid reports)
        if (!$this->report->is_validated) {
            session()->flash('error', 'Laporan ini sudah tidak valid dan tidak bisa diedit.');
            return $this->redirect(route('daily-reports.index'), navigate: true);
        }

        $this->notes = $this->report->notes;

        // Load items data
        foreach ($this->report->items as $item) {
            $this->items[$item->id_item] = [
                'initial_stock' => $item->initial_stock,
                'stock_remained' => $item->stock_remained,
                'qty_damaged' => $item->qty_damaged,
                'qty_sold' => $item->qty_sold,
            ];
        }
    }

    public function updateReport(DailyReportService $service)
    {
        $this->validate();

        try {
            $newReport = $service->editReportWithVersioning(
                oldReportId: $this->report->id,
                editorStaffId: Auth::id(),
                updatedItems: $this->items,
                notes: $this->notes
            );

            session()->flash('success', 'Laporan berhasil diperbarui! Versi baru telah dibuat.');

            return $this->redirect(route('daily-reports.show', $newReport->id), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui laporan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.daily-report-edit');
    }
}
