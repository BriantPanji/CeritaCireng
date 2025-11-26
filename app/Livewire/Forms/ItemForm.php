<?php

namespace App\Livewire\Forms;

use App\Models\Inventory;
use App\Models\Item;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ItemForm extends Form
{
    public ?Item $item = null;

//    #[Validate('required|string|max:64|unique:items,name')]
    public $name = '';

//    #[Validate('required|numeric|min:0|')]
    public $cost = 0;

//    #[Validate('required|in:BAHAN_MENTAH,BAHAN_PENUNJANG,KEMASAN|')]
    public $type = 'DEFAULT';

//    #[Validate('required|in:pcs,gr,ml,unit')]
    public $unit = 'pcs';

    #[Validate('nullable|image|max:10240', message: [
        'image' => 'Input hanya menerima gambar!',
        'max' => 'Ukuran maksimal gambar adalah 10MB'
    ])]
    public $imageFile = null;


    public $image = '';

    public function setItem(Item $item)
    {

        $this->name = $item->name;
        $this->cost = $item->cost;
        $this->type = $item->type;
        $this->unit = $item->unit;
        $this->image = $item->image;

        $this->item = $item;
    }

    public function store() {
        $this->validate([
            'name' => 'required|string|max:64|unique:items,name',
            'cost' => 'required|numeric|min:1',
            'type' => 'required|in:BAHAN_MENTAH,BAHAN_PENUNJANG,KEMASAN',
            'unit' => 'required|in:pcs,gr,ml,unit',
            'imageFile' => 'nullable|image|max:10240',
        ], [
            'name.required' => 'Nama barang wajib diisi!',
            'name.string' => 'Nama barang harus berupa teks!',
            'name.max' => 'Nama barang maksimal 64 karakter!',
            'name.unique' => 'Nama barang sudah digunakan!',
            'cost.required' => 'Harga barang wajib diisi!',
            'cost.numeric' => 'Harga barang harus berupa angka!',
            'cost.min' => 'Harga barang minimal 1!',
            'type.required' => 'Kategori barang wajib diisi!',
            'type.in' => 'Kategori barang tidak valid!',
            'unit.required' => 'Satuan barang wajib diisi!',
            'unit.in' => 'Satuan barang tidak valid!',
            'imageFile.image' => 'Input hanya menerima gambar!',
            'imageFile.max' => 'Ukuran maksimal gambar adalah 10MB',
        ]);


        if ($this->imageFile) {
            $this->image = $this->imageFile->storePublicly('items', ['disk' => 'public']);
        }

        $itemCreated = Item::create($this->only(['name', 'cost', 'type', 'unit', 'image']));
        Inventory::create([
            'id_item' => $itemCreated->id,
            'stock' => 0
        ]);
    }

    public function update() {
        $this->validate([
            'name' => 'required|string|max:64|unique:items,name',
            'cost' => 'required|numeric|min:1',
            'type' => 'required|in:BAHAN_MENTAH,BAHAN_PENUNJANG,KEMASAN',
            'unit' => 'required|in:pcs,gr,ml,unit',
            'imageFile' => 'nullable|image|max:10240',
        ], [
            'name.required' => 'Nama barang wajib diisi!',
            'name.string' => 'Nama barang harus berupa teks!',
            'name.max' => 'Nama barang maksimal 64 karakter!',
            'name.unique' => 'Nama barang sudah digunakan!',
            'cost.required' => 'Harga barang wajib diisi!',
            'cost.numeric' => 'Harga barang harus berupa angka!',
            'cost.min' => 'Harga barang minimal 1!',
            'type.required' => 'Kategori barang wajib diisi!',
            'type.in' => 'Kategori barang tidak valid!',
            'unit.required' => 'Satuan barang wajib diisi!',
            'unit.in' => 'Satuan barang tidak valid!',
            'imageFile.image' => 'Input hanya menerima gambar!',
            'imageFile.max' => 'Ukuran maksimal gambar adalah 10MB',
        ]);


        if ($this->imageFile) {
            $this->image = $this->imageFile->storePublicly('items', ['disk' => 'public']);
        }

        $this->item->update($this->only(['name', 'cost', 'type', 'unit', 'image']));
    }
}

