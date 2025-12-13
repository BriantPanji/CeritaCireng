<?php

namespace App\Services;

use App\Models\DailyOutletReport;
use App\Models\DailyOutletReportItem;
use App\Models\DailyOutletReportExpense;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Item;
use App\Models\OtherExpense;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailyReportService
{
    /**
     * Generate daily report untuk outlet tertentu
     */
    public function generateReport(
        int $outletId,
        int $staffId,
        string $reportDate,
        ?string $notes = null
    ): DailyOutletReport {
        return DB::transaction(function () use ($outletId, $staffId, $reportDate, $notes) {
            $outlet = Outlet::findOrFail($outletId);
            $staff = User::findOrFail($staffId);
            $date = Carbon::parse($reportDate);

            // Create report header
            $report = DailyOutletReport::create([
                'id_outlet' => $outletId,
                'id_staff' => $staffId,
                'report_date' => $date,
                'report_time' => now(),
                'is_validated' => true,
                'notes' => $notes,
                'created_by_name' => $staff->display_name ?? 'Unknown',
                'outlet_name' => $outlet->name,
            ]);

            // Generate item snapshots
            $this->generateItemSnapshots($report, $outletId, $date);

            return $report->load(['items']);
        });
    }

    /**
     * Generate initial item template (staff akan isi manual)
     */
    protected function generateItemSnapshots(
        DailyOutletReport $report,
        int $outletId,
        Carbon $date
    ): void {
        // Get all items untuk outlet ini
        $outletItems = DB::table('outlet_item_settings')
            ->where('id_outlet', $outletId)
            ->get();

        foreach ($outletItems as $outletItem) {
            $item = Item::find($outletItem->id_item);

            if (!$item) continue;

            // Hitung deliveries untuk hari ini (berdasarkan assigned_at karena delivery mungkin belum di-confirm)
            $delivered = DB::table('delivery_items')
                ->join('deliveries', 'deliveries.id', '=', 'delivery_items.id_delivery')
                ->where('deliveries.id_outlet', $outletId)
                ->where('delivery_items.id_item', $item->id)
                ->whereDate('deliveries.assigned_at', $date)
                ->sum('delivery_items.quantity');

            // Hitung returns untuk hari ini (sebagai referensi)
            $returned = DB::table('return_items')
                ->join('returns', 'returns.id', '=', 'return_items.id_return')
                ->join('users', 'users.id', '=', 'returns.id_staff')
                ->where('users.outlet_id', $outletId)
                ->where('return_items.id_item', $item->id)
                ->whereDate('returns.returned_at', $date)
                ->sum('return_items.quantity');

            // Create item row - STAFF AKAN ISI MANUAL
            DailyOutletReportItem::create([
                'id_outlet_report' => $report->id,
                'id_item' => $item->id,
                'item_name' => $item->name,
                'item_cost' => $item->cost,
                'item_unit' => $item->unit,
                'initial_stock' => 0, // Staff isi manual: stok di awal hari
                'stock_delivered' => $delivered ?? 0, // Auto dari delivery
                'stock_returned' => $returned ?? 0, // Auto dari return
                'qty_damaged' => 0, // Staff isi manual
                'stock_remained' => 0, // Staff isi manual: stok tersisa
                'qty_sold' => 0, // Staff isi manual: berapa yang terjual
                'total_expense' => 0, // Auto calculated: item_cost * stock_delivered
            ]);
        }
    }



    /**
     * Update item data (staff input manual)
     */
    public function updateReportItem(
        int $reportItemId,
        array $data
    ): DailyOutletReportItem {
        $reportItem = DailyOutletReportItem::findOrFail($reportItemId);

        // Update all manual fields
        $reportItem->update([
            'initial_stock' => $data['initial_stock'] ?? $reportItem->initial_stock,
            'qty_sold' => $data['qty_sold'] ?? $reportItem->qty_sold,
            'qty_damaged' => $data['qty_damaged'] ?? $reportItem->qty_damaged,
            'stock_remained' => $data['stock_remained'] ?? $reportItem->stock_remained,
        ]);

        // Auto calculate expense
        $reportItem->total_expense = $reportItem->item_cost * $reportItem->stock_delivered;
        $reportItem->save();

        return $reportItem;
    }

    /**
     * Re-validate report jika diperlukan
     */
    public function revalidateReport(int $reportId): DailyOutletReport
    {
        $report = DailyOutletReport::findOrFail($reportId);

        $report->update(['is_validated' => true]);

        return $report;
    }

    /**
     * Edit report by creating a new version (versioning system)
     * - Creates new report with updated data
     * - Marks old report as invalid
     * 
     * @param int $oldReportId ID laporan yang akan diedit
     * @param int $editorStaffId ID staff yang melakukan edit
     * @param array $updatedItems Array of updated item data [item_id => [...]]
     * @param string|null $notes Catatan baru (opsional)
     */
    public function editReportWithVersioning(
        int $oldReportId,
        int $editorStaffId,
        array $updatedItems,
        ?string $notes = null
    ): DailyOutletReport {
        return DB::transaction(function () use ($oldReportId, $editorStaffId, $updatedItems, $notes) {
            // 1. Get old report with items
            $oldReport = DailyOutletReport::with('items')->findOrFail($oldReportId);

            // 2. Verify old report is still valid (can only edit valid reports)
            if (!$oldReport->is_validated) {
                throw new \Exception('Laporan yang sudah tidak valid tidak bisa diedit.');
            }

            // 3. Mark old report as invalid
            $oldReport->update(['is_validated' => false]);

            // 4. Get editor staff info
            $editorStaff = User::findOrFail($editorStaffId);

            // 5. Create new report (copy from old with new metadata)
            $newReport = DailyOutletReport::create([
                'id_outlet' => $oldReport->id_outlet,
                'id_staff' => $editorStaffId,
                'report_date' => $oldReport->report_date,
                'report_time' => now(),
                'is_validated' => true,
                'notes' => $notes ?? $oldReport->notes,
                'created_by_name' => $editorStaff->display_name ?? $editorStaff->name ?? 'Unknown',
                'outlet_name' => $oldReport->outlet_name,
            ]);

            // 6. Copy items with updates
            foreach ($oldReport->items as $oldItem) {
                $itemId = $oldItem->id_item;
                $updates = $updatedItems[$itemId] ?? [];

                DailyOutletReportItem::create([
                    'id_outlet_report' => $newReport->id,
                    'id_item' => $oldItem->id_item,
                    'item_name' => $oldItem->item_name,
                    'item_cost' => $oldItem->item_cost,
                    'item_unit' => $oldItem->item_unit,
                    'initial_stock' => $updates['initial_stock'] ?? $oldItem->initial_stock,
                    'stock_delivered' => $oldItem->stock_delivered, // Keep auto value from system
                    'stock_returned' => $oldItem->stock_returned,   // Keep auto value from system
                    'qty_damaged' => $updates['qty_damaged'] ?? $oldItem->qty_damaged,
                    'stock_remained' => $updates['stock_remained'] ?? $oldItem->stock_remained,
                    'qty_sold' => $updates['qty_sold'] ?? $oldItem->qty_sold,
                    'total_expense' => $oldItem->total_expense,
                ]);
            }

            return $newReport->load('items');
        });
    }
}
