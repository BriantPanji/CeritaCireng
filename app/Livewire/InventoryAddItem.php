<?php

namespace App\Livewire;

use App\Models\Item;    
use Livewire\Component;


class InventoryAddItem extends Component
{
    public $showTypeDropdown = false;
    public $name;
    public $cost;
    public $unit;
    public $type;

    protected $rules = [
        'name' => 'required|min:3|unique:items,name',
        'cost' => 'required|integer|min:0',
        'unit' => 'required|in:pcs,gr,ml,unit',
        'type' => 'required|in:BAHAN_MENTAH,BAHAN_PENUNJANG,KEMASAN',
    ];

    public function save()
    {
        $this->validate($this->rules, [
            'name.required' => 'Nama item wajib diisi.',
            'name.min' => 'Nama item minimal terdiri dari 3 karakter.',
            'name.unique' => 'Nama item sudah digunakan.',
            'cost.required' => 'Harga item wajib diisi.',
            'cost.integer' => 'Harga item harus berupa angka.',
            'cost.min' => 'Harga item tidak boleh negatif.',
            'unit.required' => 'Satuan item wajib diisi.',
            'unit.in' => 'Satuan item tidak valid.',
            'type.required' => 'Tipe item wajib diisi.',
            'type.in' => 'Tipe item tidak valid.',
        ]);

        Item::create([
            'name' => $this->name,
            'cost' => $this->cost,
            'unit' => $this->unit,
            'type' => $this->type,
            
        ]);

    $this->dispatch('saved');
    
    $this->reset(['name', 'cost', 'unit', 'type']);

    return redirect('/inventory')->with('message', 'Item berhasil ditambahkan!');
    }

    public function render()
    {
        return view('livewire.inventory-add-item')->title('Tambah Item Inventaris');
    }
}
