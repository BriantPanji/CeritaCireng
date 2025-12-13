<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\ReturnModel;
use App\Models\ReturnItem;
use App\Models\ReturnConfirmation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Carbon\Carbon;

class ReturnTable extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $waktu = 'all';
    
    // For create return modal
    public $showCreateModal = false;
    public $selectedItems = [];
    public $notes = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updating($field)
    {
        if (in_array($field, ['search', 'status', 'waktu'])) {
            $this->resetPage();
        }
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->selectedItems = [];
        $this->notes = '';
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->reset(['selectedItems', 'notes']);
    }

    public function addItem()
    {
        $this->selectedItems[] = [
            'id_item' => '',
            'quantity' => 1
        ];
    }

    public function removeItem($index)
    {
        unset($this->selectedItems[$index]);
        $this->selectedItems = array_values($this->selectedItems);
    }

    public function submitReturn()
    {
        $this->validate([
            'selectedItems.*.id_item' => 'required|exists:items,id',
            'selectedItems.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000'
        ]);

        DB::transaction(function () {
            // Create return record
            $return = ReturnModel::create([
                'id_staff' => Auth::id(),
                'id_deliverer' => Auth::id(), // Same as staff for now
                'notes' => $this->notes,
                'returned_at' => now(),
            ]);

            // Create return items
            foreach ($this->selectedItems as $item) {
                ReturnItem::create([
                    'id_return' => $return->id,
                    'id_item' => $item['id_item'],
                    'quantity' => $item['quantity'],
                ]);
            }
        });

        $this->dispatch('returnCreated');
        $this->closeCreateModal();
    }

    #[On('confirmReturn')]
    public function confirmReturn($returnId)
    {
        $return = ReturnModel::with(['returnItem' => function($q) {
            $q->withPivot('quantity');
        }])->find($returnId);
        
        if (!$return) return;

        // Create confirmation
        ReturnConfirmation::create([
            'id_return' => $returnId,
            'id_inventaris' => Auth::id(),
            'confirmed_at' => now(),
        ]);

        // Update inventory stock for each returned item
        foreach ($return->returnItem as $item) {
            DB::table('inventory')
                ->where('id_item', $item->id)
                ->increment('stock', $item->pivot->quantity);
        }

        $this->dispatch('returnConfirmed');
    }

    public function getReturnsProperty()
    {
        $user = Auth::user();
        $query = ReturnModel::with(['staff', 'returnConfirmations'])
            ->with(['returnItem' => function($q) {
                $q->withPivot('quantity');
            }]);

        // Filter by role
        if (in_array($user->role->name, ['staff'])) {
            // Staff only sees their own returns
            $query->where('id_staff', $user->id);
        }

        // Filter by time
        switch ($this->waktu) {
            case 'today':
                $query->whereDate('returned_at', Carbon::today());
                break;
            case 'week':
                $query->where('returned_at', '>=', Carbon::now()->subWeek()->startOfDay());
                break;
            case 'month':
                $query->where('returned_at', '>=', Carbon::now()->subMonth()->startOfDay());
                break;
            case 'year':
                $query->where('returned_at', '>=', Carbon::now()->subYear()->startOfDay());
                break;
        }

        // Search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->whereHas('staff', function ($subQ) {
                    $subQ->where('display_name', 'like', '%' . $this->search . '%');
                });
            });
        }

        return $query->orderBy('returned_at', 'desc')->paginate(4);
    }

    public function render()
    {
        $userRole = Auth::user()->role->name;
        
        return view('livewire.return-table', [
            'returns' => $this->returns,
            'items' => Item::all(),
            'isStaff' => in_array($userRole, ['staff']),
            'isAdmin' => in_array($userRole, ['dev', 'admin', 'inventaris']),
        ]);
    }
}
