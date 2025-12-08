<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\Inventory;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class StockTable extends Component
{
    use WithPagination;

    public $query = '';
    public $filterType = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';

    // Stock adjustment properties
    public $showAddStockModal = false;
    public $showReduceStockModal = false;
    public $selectedItemId = null;
    public $selectedItemName = '';
    public $selectedItemCurrentStock = 0;
    public $adjustmentAmount = 0;

    protected $queryString = [
        'query' => ['except' => ''],
        'filterType' => ['except' => ''],
    ];

    protected function rules()
    {
        return [
            'selectedItemId' => 'required|exists:items,id',
            'adjustmentAmount' => 'required|integer|min:1',
        ];
    }

    protected function messages()
    {
        return [
            'selectedItemId.required' => 'Silakan pilih item terlebih dahulu.',
            'selectedItemId.exists' => 'Item tidak ditemukan.',
            'adjustmentAmount.required' => 'Jumlah harus diisi.',
            'adjustmentAmount.integer' => 'Jumlah harus berupa angka.',
            'adjustmentAmount.min' => 'Jumlah minimal adalah 1.',
        ];
    }

    public function updatedQuery()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    // Open add stock modal
    public function openAddStockModal()
    {
        $this->reset(['selectedItemId', 'adjustmentAmount', 'selectedItemName', 'selectedItemCurrentStock']);
        $this->showAddStockModal = true;
        $this->dispatch('modal-opened');
    }

    // Open reduce stock modal for specific item
    public function openReduceStockModal($itemId)
    {
        $item = Item::with('stock')->find($itemId);

        if ($item) {
            $this->selectedItemId = $item->id;
            $this->selectedItemName = $item->name;
            $this->selectedItemCurrentStock = $item->stock ? $item->stock->stock : 0;
            $this->adjustmentAmount = 0;
            $this->showReduceStockModal = true;
            $this->dispatch('modal-opened');
        }
    }

    // Close all modals
    public function closeStockModals()
    {
        $this->showAddStockModal = false;
        $this->showReduceStockModal = false;
        $this->reset(['selectedItemId', 'adjustmentAmount', 'selectedItemName', 'selectedItemCurrentStock']);
        $this->dispatch('modal-closed');
    }

    // Trigger confirmation for add stock
    public function confirmAddStock()
    {
        $this->validate();

        $item = Item::find($this->selectedItemId);

        if (!$item) {
            $this->dispatch('show-error', message: 'Item tidak ditemukan');
            return;
        }

        $this->dispatch(
            'confirm-add-stock',
            itemName: $item->name,
            amount: $this->adjustmentAmount
        );
    }

    // Trigger confirmation for reduce stock
    public function confirmReduceStock()
    {
        $this->validate();

        // Additional validation for reduce
        if ($this->adjustmentAmount > $this->selectedItemCurrentStock) {
            $this->addError('adjustmentAmount', 'Jumlah pengurangan tidak boleh melebihi stok yang tersedia (' . $this->selectedItemCurrentStock . ')');
            return;
        }

        $this->dispatch(
            'confirm-reduce-stock',
            itemName: $this->selectedItemName,
            amount: $this->adjustmentAmount,
            currentStock: $this->selectedItemCurrentStock
        );
    }

    // Actually add stock (called after confirmation)
    #[On('executeAddStock')]
    public function addStock()
    {
        $this->validate();

        try {
            $inventory = Inventory::where('id_item', $this->selectedItemId)->first();

            if ($inventory) {
                $inventory->stock += $this->adjustmentAmount;
                $inventory->save();
            } else {
                Inventory::create([
                    'id_item' => $this->selectedItemId,
                    'stock' => $this->adjustmentAmount,
                ]);
            }

            $this->closeStockModals();
            $this->dispatch('stock-updated', message: 'Stok berhasil ditambahkan!');
        } catch (\Exception $e) {
            $this->dispatch('show-error', message: 'Gagal menambahkan stok: ' . $e->getMessage());
        }
    }

    // Actually reduce stock (called after confirmation)
    #[On('executeReduceStock')]
    public function reduceStock()
    {
        $this->validate();

        try {
            $inventory = Inventory::where('id_item', $this->selectedItemId)->first();

            if (!$inventory) {
                $this->dispatch('show-error', message: 'Data stok tidak ditemukan');
                return;
            }

            if ($this->adjustmentAmount > $inventory->stock) {
                $this->dispatch('show-error', message: 'Jumlah pengurangan melebihi stok yang tersedia');
                return;
            }

            $inventory->stock -= $this->adjustmentAmount;
            $inventory->save();

            $this->closeStockModals();
            $this->dispatch('stock-updated', message: 'Stok berhasil dikurangi!');
        } catch (\Exception $e) {
            $this->dispatch('show-error', message: 'Gagal mengurangi stok: ' . $e->getMessage());
        }
    }

    // Computed property for table colspan based on user role
    public function getColspanAttribute()
    {
        return auth()->check() && in_array(auth()->user()->role->name, ['inventaris', 'admin', 'dev']) ? 8 : 7;
    }


    public function render()
    {
        $items = Item::with('stock')
            ->when($this->query, function ($q) {
                $q->where('name', 'like', '%' . $this->query . '%');
            })
            ->when($this->filterType, function ($q) {
                $q->where('type', $this->filterType);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        return view('livewire.stock-table', [
            'items' => $items,
        ]);
    }
}
