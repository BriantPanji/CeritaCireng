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

class DeliveryAdd extends Component
{
    public $id_kurir = '';
    public $id_outlet = '';

    // For dynamic item rows
    public $deliveryItems = [];


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

    public function save()
    {
        $this->validate();

        // Check if there's at least one item
        if (empty($this->deliveryItems)) {
            session()->flash('error', 'Minimal harus ada satu item dalam pengiriman.');
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
            'couriers' => User::where('role_id', 3)->where('status', 'AKTIF')->get(),
            'outlets' => Outlet::where('status', 'AKTIF')->get(),
            'items' => Item::with('stock')->get(),
        ]);
    }
}
