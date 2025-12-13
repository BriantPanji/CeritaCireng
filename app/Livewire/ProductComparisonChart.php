<?php

namespace App\Livewire;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\DailyOutletReportItem;
use App\Models\Item;
use Carbon\Carbon;
use Livewire\Component;

class ProductComparisonChart extends Component
{
    public $timeFilter = 'week';

    public function setFilter($filter)
    {
        $this->timeFilter = $filter;
    }

    public function getChartDataProperty()
    {
        // Calculate date range based on filter
        $startDate = match ($this->timeFilter) {
            'week' => Carbon::now()->subDays(7)->startOfDay(),
            'month' => Carbon::now()->subDays(30)->startOfDay(),
            'year' => Carbon::now()->subDays(365)->startOfDay(),
            default => Carbon::now()->subDays(7)->startOfDay(),
        };

        // Get items 1-5 with their names
        $items = Item::whereIn('id', [1, 2, 3, 4, 5])->get();
        
        $labels = [];
        $receivedData = [];
        $soldData = [];

        foreach ($items as $item) {
            $labels[] = $item->name;

            // Get current user's outlet
            $userOutletId = auth()->user()->outlet_id;

            // Calculate RECEIVED quantities (from completed deliveries to this outlet)
            $received = DeliveryItem::whereHas('delivery', function ($query) use ($startDate, $userOutletId) {
                $query->where('status', 'SELESAI')
                    ->where('id_outlet', $userOutletId) // Filter by staff's outlet
                    ->whereHas('hasDeliveryConfirmation', function ($q) use ($startDate) {
                        $q->where('received_at', '>=', $startDate);
                    });
            })
            ->where('id_item', $item->id)
            ->sum('quantity');

            // Calculate SOLD quantities (from daily reports of this outlet)
            $sold = DailyOutletReportItem::whereHas('report', function ($query) use ($startDate, $userOutletId) {
                $query->where('report_date', '>=', $startDate)
                    ->where('id_outlet', $userOutletId); // Filter by staff's outlet
            })
            ->where('id_item', $item->id)
            ->sum('qty_sold');

            $receivedData[] = $received ?? 0;
            $soldData[] = $sold ?? 0;
        }

        return [
            'labels' => $labels,
            'received' => $receivedData,
            'sold' => $soldData,
        ];
    }

    public function render()
    {
        return view('livewire.product-comparison-chart', [
            'chartData' => $this->chartData,
        ]);
    }
}
