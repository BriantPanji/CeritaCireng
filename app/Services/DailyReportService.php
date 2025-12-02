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

        // Update manual fields
        $reportItem->update([
            'qty_sold' => $data['qty_sold'] ?? $reportItem->qty_sold,
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
}
