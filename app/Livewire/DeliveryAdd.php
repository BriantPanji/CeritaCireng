<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\User;
use App\Models\Outlet;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryAdd extends Component
{
    public $id_kurir = '';
    public $id_outlet = '';

    // For dynamic item rows
    public $deliveryItems = [];

    // Stock data for validation
    public $itemStocks = [];

    public $showSuccessModal = false;
    public $createdDeliveryId = null;

    protected $rules = [
        'id_kurir' => 'required|exists:users,id',
        'id_outlet' => 'required|exists:outlets,id',
        'deliveryItems.*.id_item' => 'required|exists:items,id',
        'deliveryItems.*.quantity' => 'required|integer|min:1',
    ];

    protected $messages = [
        'id_kurir.required' => 'Kurir harus dipilih.',
        'id_kurir.exists' => 'Kurir tidak valid.',
        'id_outlet.required' => 'Outlet harus dipilih.',
        'id_outlet.exists' => 'Outlet tidak valid.',
        'deliveryItems.*.id_item.required' => 'Item harus dipilih.',
        'deliveryItems.*.id_item.exists' => 'Item tidak valid.',
        'deliveryItems.*.quantity.required' => 'Jumlah harus diisi.',
        'deliveryItems.*.quantity.integer' => 'Jumlah harus berupa angka.',
        'deliveryItems.*.quantity.min' => 'Jumlah minimal 1.',
    ];

    public function mount()
    {
        // Initialize with one empty item row
        $this->deliveryItems = [
            ['id_item' => '', 'quantity' => 1]
        ];
        $this->loadItemStocks();
    }

    /**
     * Load all item stocks for validation
     */
    public function loadItemStocks()
    {
        $items = Item::with('stock')->get();
        $this->itemStocks = [];
        foreach ($items as $item) {
            $this->itemStocks[$item->id] = $item->stock ? $item->stock->stock : 0;
        }
    }

    /**
     * When outlet is selected, load default items from outlet_item_settings
     */
    public function updatedIdOutlet($value)
    {
        if (empty($value)) {
            $this->deliveryItems = [
                ['id_item' => '', 'quantity' => 1]
            ];
            return;
        }

        // Get default items for this outlet from outlet_item_settings
        $outletSettings = DB::table('outlet_item_settings')
            ->where('id_outlet', $value)
            ->get();

        if ($outletSettings->isEmpty()) {
            // No settings found, keep one empty row
            $this->deliveryItems = [
                ['id_item' => '', 'quantity' => 1]
            ];
            return;
        }

        // Populate deliveryItems with outlet settings
        $this->deliveryItems = [];
        foreach ($outletSettings as $setting) {
            $this->deliveryItems[] = [
                'id_item' => (string) $setting->id_item,
                'quantity' => $setting->quantity ?? 1,
            ];
        }
    }

    public function addItemRow()
    {
        $this->deliveryItems[] = ['id_item' => '', 'quantity' => 1];
    }

    public function removeItemRow($index)
    {
        if (count($this->deliveryItems) > 1) {
            unset($this->deliveryItems[$index]);
            $this->deliveryItems = array_values($this->deliveryItems);
        }
    }

    /**
     * Validate that quantities don't exceed available stock
     */
    public function validateStockQuantities(): array
    {
        $errors = [];

        // Group items by id_item and sum their quantities
        $groupedItems = [];
        foreach ($this->deliveryItems as $index => $item) {
            if (empty($item['id_item'])) continue;

            $itemId = $item['id_item'];
            $quantity = (int) $item['quantity'];

            if (!isset($groupedItems[$itemId])) {
                $groupedItems[$itemId] = [
                    'total' => 0,
                    'indexes' => []
                ];
            }
            $groupedItems[$itemId]['total'] += $quantity;
            $groupedItems[$itemId]['indexes'][] = $index;
        }

        // Check each grouped item against stock
        foreach ($groupedItems as $itemId => $data) {
            $availableStock = $this->itemStocks[$itemId] ?? 0;
            if ($data['total'] > $availableStock) {
                $item = Item::find($itemId);
                $itemName = $item ? $item->name : "Item #{$itemId}";
                $errors[] = "Total jumlah \"{$itemName}\" ({$data['total']}) melebihi stok yang tersedia ({$availableStock}).";
            }
        }

        return $errors;
    }

    public function save()
    {
        $this->validate();

        // Check if there's at least one item
        if (empty($this->deliveryItems)) {
            session()->flash('error', 'Minimal harus ada satu item dalam pengiriman.');
            return;
        }

        // Validate stock quantities
        $stockErrors = $this->validateStockQuantities();
        if (!empty($stockErrors)) {
            session()->flash('error', implode(' ', $stockErrors));
            return;
        }

        try {
            // Get the authenticated inventaris user
            $id_inventaris = Auth::id();

            // Create the delivery
            $delivery = Delivery::create([
                'id_inventaris' => $id_inventaris,
                'id_kurir' => $this->id_kurir,
                'id_outlet' => $this->id_outlet,
                'status' => 'DITUGASKAN',
                'assigned_at' => Carbon::now(),
            ]);

            // Create delivery items
            // Group items by id_item and sum their quantities
            $groupedItems = [];
            foreach ($this->deliveryItems as $item) {
                $itemId = $item['id_item'];
                $quantity = $item['quantity'];

                if (isset($groupedItems[$itemId])) {
                    // Item already exists, add to quantity
                    $groupedItems[$itemId] += $quantity;
                } else {
                    // New item, set quantity
                    $groupedItems[$itemId] = $quantity;
                }
            }

            // Create one row per unique item with total quantity
            foreach ($groupedItems as $itemId => $totalQuantity) {
                DeliveryItem::create([
                    'id_delivery' => $delivery->id,
                    'id_item' => $itemId,
                    'quantity' => $totalQuantity,
                ]);
            }

            $this->createdDeliveryId = $delivery->id;
            $this->showSuccessModal = true;

            // Reset form
            $this->reset(['id_kurir', 'id_outlet', 'deliveryItems']);
            $this->deliveryItems = [
                ['id_item' => '', 'quantity' => 1]
            ];
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function closeSuccessModal()
    {
        $this->showSuccessModal = false;
        $this->createdDeliveryId = null;
    }

    public function render()
    {
        return view('livewire.delivery-add', [
            'couriers' => User::where('role_id', 4)->where('status', 'AKTIF')->get(),
            'outlets' => Outlet::where('status', 'AKTIF')->get(),
            'items' => Item::with('stock')->get(),
        ]);
    }
}
