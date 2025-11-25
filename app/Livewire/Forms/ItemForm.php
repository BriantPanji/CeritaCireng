<?php

namespace App\Livewire\Forms;

use App\Models\Item;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ItemForm extends Form
{
    public ?Item $item = null;

    #[Validate('required|string|max:64|unique:items,name')]
    public $name = '';

    #[Validate('required|numeric|min:0|')]
    public $cost = 0;

    #[Validate('required|in:BAHAN_MENTAH,BAHAN_PENUNJANG,KEMASAN|')]
    public $type = 'BAHAN_PENUNJANG';

    #[Validate('required|in:pcs,gr,ml,unit')]
    public $unit = 'pcs';

    #[Validate('required|image|max:2048')]
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
        $this->validate();

        if ($this->imageFile) {
            // storePublicly expects 'disk' key
            $this->image = $this->imageFile->storePublicly('items', ['disk' => 'public']);
        }

        Item::create($this->only(['name', 'cost', 'type', 'unit', 'image']));
    }
}
