<?php

namespace App\Livewire;

use App\Models\Delivery;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class ReceivedItemsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $waktu = 'all';

    protected $queryString = [
        'search' => ['except' => ''],
        'waktu' => ['except' => 'all'],
    ];

    public function updating($field)
    {
        if (in_array($field, ['search', 'waktu'])) {
            $this->resetPage();
        }
    }

    public function getReceivedItemsProperty()
    {
        $query = Delivery::with(['items.item', 'hasDeliveryConfirmation'])
            ->where('status', 'SELESAI');

        // Filter by time
        switch ($this->waktu) {
            case 'today':
                $query->whereHas('hasDeliveryConfirmation', function($q) {
                    $q->whereDate('received_at', Carbon::today());
                });
                break;
            case 'week':
                $query->whereHas('hasDeliveryConfirmation', function($q) {
                    $q->where('received_at', '>=', Carbon::now()->subWeek()->startOfDay());
                });
                break;
            case 'month':
                $query->whereHas('hasDeliveryConfirmation', function($q) {
                    $q->where('received_at', '>=', Carbon::now()->subMonth()->startOfDay());
                });
                break;
            case 'year':
                $query->whereHas('hasDeliveryConfirmation', function($q) {
                    $q->where('received_at', '>=', Carbon::now()->subYear()->startOfDay());
                });
                break;
        }

        $deliveries = $query->orderBy('assigned_at', 'desc')->get();

        // Flatten items and apply search filter at item level
        $items = collect();
        foreach ($deliveries as $delivery) {
            foreach ($delivery->items as $item) {
                // Only add item if it matches search term (or no search term)
                if (empty($this->search) || stripos($item->item->name, $this->search) !== false) {
                    $items->push([
                        'item_name' => $item->item->name,
                        'quantity' => $item->quantity,
                        'received_at' => $delivery->hasDeliveryConfirmation->received_at ?? null,
                    ]);
                }
            }
        }

        // Manual pagination
        $perPage = 8;
        $currentPage = $this->getPage();
        $offset = ($currentPage - 1) * $perPage;
        
        $paginatedItems = $items->slice($offset, $perPage)->values();
        
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $items->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function render()
    {
        return view('livewire.received-items-table', [
            'receivedItems' => $this->receivedItems,
        ]);
    }
}
