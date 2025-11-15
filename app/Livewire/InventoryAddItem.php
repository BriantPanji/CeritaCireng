<?php

namespace App\Livewire;

use App\Models\Item;    
use Livewire\Component;
use Livewire\WithFileUploads;

class InventoryAddItem extends Component
{
    use WithFileUploads;

    public $showTypeDropdown = false;
    public $image;
    public $name;
    public $cost;
    public $unit;
    public $type;

    protected $rules = [
        'name' => 'required|min:3|unique:items,name',
        'cost' => 'required|integer|min:0',
        'unit' => 'required|in:pcs,gr,ml,unit',
        'type' => 'required|in:BAHAN_MENTAH,BAHAN_PENUNJANG,KEMASAN',
        'image' => 'nullable|image|max:10240', 
    ];

    protected $messages = [
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
        'image.image' => 'File harus berupa gambar.',
        'image.max' => 'Ukuran gambar maksimal 2MB.',
    ];

    public function updatedImage()
    {
        dd(this->image);
        $this->validate([
            'image' => 'image|max:2048',
        ]);
    }

    public function save()
    {
        $this->validate();

        try {
            $imageName = 'default.png';
            
            if ($this->image) {
                $imageName = $this->image->store('items', 'public');
            }

            Item::create([
                'name' => $this->name,
                'cost' => $this->cost,
                'unit' => $this->unit,
                'type' => $this->type,
                'image' => $imageName,
            ]);

            session()->flash('message', 'Item berhasil ditambahkan!');
            
            return redirect('/inventory');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.inventory-add-item')->title('Tambah Item Inventaris');
    }
}