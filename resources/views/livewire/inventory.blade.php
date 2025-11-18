<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use App\Models\Item;
use App\Models\Inventory;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new #[Layout('components.layouts.app'), Title('Inventaris / Gudang')] class extends Component {
    use WithPagination, WithFileUploads;

    public $query = '';
    public $itemDetailById = null;
    public $showModal = false;
    public $showModalDelete = false;
    public $showModalDeleteDone = false;
    
    // Add item
    public $showAdd = false;
    
    #[Validate('nullable|image|max:2048')]
    public $image;
    
    // Edit item
    public $showEdit = false;
    public $editingItemId = null;
    
    #[Validate('nullable|image|max:2048')]
    public $newImage;
    
    // Form data
    public $form = [
        'name' => '',
        'type' => '',
        'cost' => '',
        'unit' => '',
    ];

    public function updatedQuery()
    {
        $this->resetPage();
    }

    public function with()
    {
        return [
            'items' => Item::with('stock')
                ->when($this->query, function ($q) {
                    $q->where('name', 'like', '%' . $this->query . '%');
                })
                ->paginate(10),
        ];
    }

    public function showDetail($id)
    {
        $this->itemDetailById = Item::with('stock')->find($id);
        $this->showModal = true;
        $this->dispatch('modal-opened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->showModalDelete = false;
        $this->itemDetailById = null;
        $this->dispatch('modal-closed');
    }

    public function deleteItem($id)
    {
        $item = Item::find($id);
        if ($item && $item->image && $item->image !== 'https://placehold.co/600x400.webp?text=Foto+Item') {
            // Extract path from URL if it's a storage URL
            $path = str_replace('/storage/', '', parse_url($item->image, PHP_URL_PATH));
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
        Item::destroy($id);
        $this->showModalDelete = false;
        $this->showModalDeleteDone = true;
        $this->itemDetailById = null;
        $this->showModal = false;
    }

    public function closeModalDeleteDone()
    {
        $this->showModalDeleteDone = false;
        $this->dispatch('modal-closed');
    }
    
    // Add Item Methods
    public function openAddModal()
    {
        $this->resetForm();
        $this->showAdd = true;
        $this->dispatch('modal-opened');
    }
    
    public function closeModalAdd()
    {
        $this->showAdd = false;
        $this->resetForm();
        $this->dispatch('modal-closed');
    }
    
    public function saveItem()
    {
        $this->validate([
            'form.name' => 'required|string|max:64|unique:items,name',
            'form.type' => 'required|in:BAHAN_MENTAH,BAHAN_PENUNJANG,KEMASAN',
            'form.cost' => 'required|integer|min:0',
            'form.unit' => 'required|in:pcs,gr,ml,unit',
        ]);
        
        $imagePath = 'https://placehold.co/600x400.webp?text=Foto+Item';
        
        if ($this->image) {
            $filename = time() . '_' . $this->image->getClientOriginalName();
            $path = $this->image->storeAs('items', $filename, 'public');
            $imagePath = '/storage/' . $path;
        }
        
        $item = Item::create([
            'name' => $this->form['name'],
            'type' => $this->form['type'],
            'cost' => $this->form['cost'],
            'unit' => $this->form['unit'],
            'image' => $imagePath,
        ]);
        
        // Create inventory record with stock 0
        Inventory::create([
            'id_item' => $item->id,
            'stock' => 0,
        ]);
        
        $this->closeModalAdd();
        $this->reset('image');
        session()->flash('message', 'Barang berhasil ditambahkan!');
    }
    
    // Edit Item Methods
    public function openEditModal($id)
    {
        $item = Item::find($id);
        if ($item) {
            $this->editingItemId = $id;
            $this->form = [
                'name' => $item->name,
                'type' => $item->type,
                'cost' => $item->cost,
                'unit' => $item->unit,
            ];
            $this->itemDetailById = $item;
            $this->showEdit = true;
            $this->showModal = false;
            $this->dispatch('modal-opened');
        }
    }
    
    public function closeModalEdit()
    {
        $this->showEdit = false;
        $this->editingItemId = null;
        $this->itemDetailById = null;
        $this->resetForm();
        $this->reset('newImage');
        $this->dispatch('modal-closed');
    }
    
    public function updateItem()
    {
        $this->validate([
            'form.name' => 'required|string|max:64|unique:items,name,' . $this->editingItemId,
            'form.type' => 'required|in:BAHAN_MENTAH,BAHAN_PENUNJANG,KEMASAN',
            'form.cost' => 'required|integer|min:0',
            'form.unit' => 'required|in:pcs,gr,ml,unit',
        ]);
        
        $item = Item::find($this->editingItemId);
        
        if ($item) {
            $updateData = [
                'name' => $this->form['name'],
                'type' => $this->form['type'],
                'cost' => $this->form['cost'],
                'unit' => $this->form['unit'],
            ];
            
            if ($this->newImage) {
                // Delete old image if exists and not placeholder
                if ($item->image && $item->image !== 'https://placehold.co/600x400.webp?text=Foto+Item') {
                    $path = str_replace('/storage/', '', parse_url($item->image, PHP_URL_PATH));
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
                
                // Save new image
                $filename = time() . '_' . $this->newImage->getClientOriginalName();
                $path = $this->newImage->storeAs('items', $filename, 'public');
                $updateData['image'] = '/storage/' . $path;
            }
            
            $item->update($updateData);
            
            $this->closeModalEdit();
            $this->reset('newImage');
            session()->flash('message', 'Barang berhasil diperbarui!');
        }
    }
    
    private function resetForm()
    {
        $this->form = [
            'name' => '',
            'type' => '',
            'cost' => '',
            'unit' => '',
        ];
        $this->reset('image', 'newImage');
    }
}; ?>

<div class="min-w-full max-w-full" 
    x-data 
    @modal-opened.window="document.body.style.overflow = 'hidden'"
    @modal-closed.window="document.body.style.overflow = 'auto'">
    
    {{-- Success Message --}}
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('message') }}
        </div>
    @endif
    
    {{-- Header with Add Button --}}
    <div class="min-w-full max-w-full h-fit mb-4 flex justify-between items-center gap-4">
        <div x-data="{xShow: false}" class="flex-1 max-h-full group relative">
            <span onclick="document.getElementById('cari').focus()"
                class="max-h-full min-h-full w-9 absolute flex cursor-text items-center justify-center">
                <i class="ph ph-magnifying-glass"></i>
            </span>
            <input wire:model.live.debounce.300ms="query" @input.debounce.250ms="xShow = $event.target.value.length > 0" type="text" role="searchbox" name="cari" id="cari"
                autocomplete="off" placeholder="Cari barang..."
                class="w-full px-9 p-1 max-h-10 h-10 text-xs ring-[0.5px] ring-gray-400 rounded-xl outline-none group-hover:ring-primary group-hover:ring-[0.5px] focus:ring-primary-200 focus:ring-[0.5px]" />
            <button type="button" x-show="xShow" onclick="document.getElementById('cari').focus();" @click="xShow = false" wire:click="$set('query', '')"
                class="absolute right-0 top-0 max-h-full min-h-full w-9 flex items-center justify-center cursor-pointer">
                <i class="ph ph-x text-lg"></i>
            </button>
        </div>
        <button type="button" wire:click="openAddModal"
            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition flex items-center gap-2 whitespace-nowrap">
            <i class="ph ph-plus text-lg"></i>
            <span class="hidden sm:inline">Tambah Barang</span>
        </button>
    </div>

    <section class="min-w-full max-w-full min-h-[75vh] flex flex-wrap justify-evenly gap-4">
        @forelse ($items as $item)
            <article wire:key="item-{{ $item->id }}"
                class="min-w-full max-w-full min-h-10 xs:!max-w-[47%] xs:min-w-[47%] lg:!max-w-[31%] lg:min-w-[31%] h-fit bg-white rounded-xl">
                <div  wire:dblclick="showDetail({{ $item->id }})" class="min-w-full max-w-full aspect-[4/3] bg-gray-400 rounded-t-xl object-cover">
                    <img src="{{ $item->image }}" class="max-h-full min-w-full aspect-[4/3] object-cover rounded-t-xl"
                        alt="">
                </div>
                <div class="min-h-22 max-h-full min-w-full max-w-full p-4 flex flex-col justify-between">
                    <h3 class="text-lg xs:text-base font w-fit max-w-full truncate cursor-pointer" wire:click="showDetail({{ $item->id }})">{{ \Str::title($item->name) }}</h3>
                    <span class="text-sm min-w-full max-w-full flex items-center justify-between">
                        <span class="font-light xs:text-xs text-neutral-400">Jumlah: {{ $item->stock?->stock ?? 0 }}
                            {{ ($item->unit) }}</span>
                        <button type="button" wire:click="showDetail({{ $item->id }})"
                            class="text-primary/80 select-none text-[.8rem] cursor-pointer hover:underline">Detail</button>
                    </span>
                </div>
            </article>
        @empty
            <div class="w-full p-8 text-center text-gray-600">
                @if ($query)
                    <p class="text-lg font-medium">Pencarian Tidak Ditemukan</p>
                    <p class="text-sm mt-2">Tidak ada barang yang sesuai dengan kata kunci
                        "<strong>{{ $query }}</strong>".</p>
                @else
                    <p class="text-lg font-medium">Belum ada barang</p>
                    <p class="text-sm mt-2">Tambahkan barang baru untuk mengisi inventaris.</p>
                @endif
            </div>
        @endforelse

        <div class="w-full">
            {{ $items->links('vendor.pagination.custom') }}
        </div>
    </section>

    {{-- Modal Detail --}}
    @if ($showModal && $itemDetailById)
        <div x-data x-show="true" x-transition.opacity @click.self="$wire.closeModal()"
            class="fixed inset-0  bg-black/30 flex items-center justify-center p-4 z-50">

            <div @click.stop class="bg-white no-scrollbar rounded-2xl max-w-3xl w-full xs:w-sm lg:w-3xl max-h-[90vh] overflow-y-auto shadow-xl">

                <div
                    class="sticky top-0 bg-white border-b border-gray-200 px-4 pt-3 pb-2 flex items-center justify-between rounded-t-2xl">
                    <h2 class="text-lg font-medium">Detail Barang</h2>
                    <button type="button" wire:click="closeModal"
                        class="cursor-pointer text-gray-400 hover:text-gray-600">
                        <i class="ph ph-x text-2xl"></i>
                    </button>
                </div>

                <div class="p-6 space-y-6 mb-2 lg:space-y-0 lg:mb-0 lg:flex lg:gap-6 lg:items-center">
                    <div class="w-full aspect-[4/3] lg:min-w-2/5 lg:max-w-2/5 bg-gray-200 rounded-xl overflow-hidden">
                        <img src="{{ $itemDetailById->image }}" class="w-full h-full object-cover"
                            alt="{{ $itemDetailById->name }}">
                    </div>

                    <div class="space-y-4 lg:space-y-3 lg:w-full">
                        <div>
                            <label class="text-sm text-gray-500">Nama Barang</label>
                            <p class="text-lg font-medium">{{ \Str::title($itemDetailById->name) }}</p>
                        </div>

                        <div class="min-w-full max-w-full lg:pb-3">
                            <table class="**:px-0.5">
                                <tr class="border-y border-gray-200">
                                    <td class="py-2 lg:py-1.5 text-gray-600 w-[40%]">Kategori</td>
                                    <td class="py-2 lg:py-1.5 w-[1%]">:</td>
                                    <td class="py-2 lg:py-1.5 font-medium text-right w-[49%]">
                                        {{ \Str::title(\Str::replace('_', ' ', $itemDetailById->type)) }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="py-2 lg:py-1.5 text-gray-600 w-[40%]">Stok</td>
                                    <td class="py-2 lg:py-1.5 w-[1%]">:</td>
                                    <td class="py-2 lg:py-1.5 font-medium text-right w-[59%]">{{ $itemDetailById->stock?->stock ?? 0 }}
                                        {{ ($itemDetailById->unit) }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="py-2 lg:py-1.5 text-gray-600 w-[40%]">Harga Satuan</td>
                                    <td class="py-2 lg:py-1.5 w-[1%]">:</td>
                                    <td class="py-2 lg:py-1.5 font-medium text-right w-[59%]">@convertRupiah($itemDetailById->cost) /
                                        {{ ($itemDetailById->unit) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div
                    class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-4 py-2.5 flex justify-end gap-2 rounded-b-2xl">
                    <button type="button" wire:click="$set('showModalDelete', true)"
                        class="px-4 py-1 cursor-pointer bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Hapus
                    </button>
                    <button type="button" wire:click="openEditModal({{ $itemDetailById->id }})"
                        class="px-4 py-1 cursor-pointer bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                        Edit
                    </button>
                    <button autofocus type="button" wire:click="closeModal"
                        class="px-4 py-1 cursor-pointer focus:shadow-[0_0_0_2px_#111] bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showModalDelete && $itemDetailById)
        <div x-data x-show="true" x-transition.opacity @click.self="$wire.showModalDelete = false"
            class="fixed inset-0 bg-black/30 flex items-center justify-center p-4 z-50">

            <div @click.stop class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">

                <h2 class="text-xl font-semibold mb-4">Konfirmasi Hapus Barang</h2>
                <p class="mb-6">Apakah Anda yakin ingin menghapus barang
                    <strong>{{ \Str::title($itemDetailById->name) }}</strong>? Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="$set('showModalDelete', false)"
                        class="px-4 py-1 cursor-pointer bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="button" wire:click="deleteItem({{ $itemDetailById->id }})"
                        class="px-4 py-1 cursor-pointer bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showModalDeleteDone)
        <div x-data x-show="true" x-transition.opacity @click.self="$wire.showModalDeleteDone = false"
            class="fixed inset-0 bg-black/30 flex items-center justify-center p-4 z-50">

            <div @click.stop class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl text-center">

                <h2 class="text-xl font-semibold mb-4">Barang Dihapus</h2>
                <p class="mb-6">Barang telah berhasil dihapus dari inventaris.</p>
                <div class="flex justify-center">
                    <button type="button" wire:click="closeModalDeleteDone"
                        class="px-4 py-1 cursor-pointer bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Add Item Modal --}}
    @if ($showAdd)
        <div x-data x-show="true" x-transition.opacity @click.self="$wire.closeModalAdd()"
            class="fixed inset-0 bg-black/30 flex items-center justify-center p-4 z-50">

            <div @click.stop class="bg-white no-scrollbar rounded-2xl max-w-3xl w-full xs:w-sm lg:w-3xl max-h-[90vh] overflow-y-auto shadow-xl">
                
                {{-- Header --}}
                <div class="sticky top-0 bg-white border-b border-gray-200 px-4 pt-3 pb-2 flex items-center justify-between rounded-t-2xl">
                    <h2 class="text-lg font-medium">Tambah Barang Baru</h2>
                    <button type="button" wire:click="closeModalAdd"
                        class="cursor-pointer text-gray-400 hover:text-gray-600">
                        <i class="ph ph-x text-2xl"></i>
                    </button>
                </div>

                {{-- Form Content --}}
                <div class="p-6">
                    <form wire:submit="saveItem" class="space-y-4">
                        
                        {{-- Image Upload --}}
                        <div class="w-full">
                            <label class="text-sm font-medium text-gray-700 mb-2 block">
                                Upload Gambar
                            </label>
                            
                            <div x-data="{ 
                                isDragging: false,
                                handleDrop(e) {
                                    this.isDragging = false;
                                    const files = e.dataTransfer.files;
                                    if (files.length > 0) {
                                        @this.upload('image', files[0]);
                                    }
                                }
                            }"
                                @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="handleDrop"
                                :class="isDragging ? 'border-primary bg-primary/5' : 'border-gray-300'"
                                class="relative border-2 border-dashed rounded-lg p-6 transition-colors">
                                
                                <div class="text-center">
                                    <i class="ph ph-upload-simple text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-600 mb-2">
                                        Drag & drop gambar di sini atau
                                    </p>
                                    <label class="cursor-pointer inline-block px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                                        <span>Pilih File</span>
                                        <input type="file" wire:model="image" accept="image/*" class="hidden">
                                    </label>
                                    <p class="text-xs text-gray-500 mt-2">PNG, JPG, GIF hingga 2MB</p>
                                </div>
                            </div>
                            
                            @error('image')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            
                            {{-- Preview --}}
                            <div wire:loading wire:target="image" class="text-sm text-gray-500 mt-2 flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Uploading...
                            </div>
                            @if($image)
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600 mb-2">Preview:</p>
                                    <div class="w-full aspect-[4/3] lg:max-w-xs bg-gray-200 rounded-xl overflow-hidden relative">
                                        <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover" alt="Preview">
                                        <button type="button" wire:click="$set('image', null)" 
                                            class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700">
                                            <i class="ph ph-x text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Name --}}
                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-2 block">Nama Barang *</label>
                            <input type="text" wire:model="form.name" placeholder="Contoh: Tepung Terigu"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition">
                            @error('form.name')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Type --}}
                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-2 block">Kategori *</label>
                            <select wire:model="form.type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition">
                                <option value="">Pilih Kategori</option>
                                <option value="BAHAN_MENTAH">Bahan Mentah</option>
                                <option value="BAHAN_PENUNJANG">Bahan Penunjang</option>
                                <option value="KEMASAN">Kemasan</option>
                            </select>
                            @error('form.type')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Cost --}}
                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-2 block">Harga Satuan (Rp) *</label>
                            <input type="number" wire:model="form.cost" min="0" placeholder="Contoh: 15000"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition">
                            @error('form.cost')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Unit --}}
                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-2 block">Satuan *</label>
                            <select wire:model="form.unit"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition">
                                <option value="">Pilih Satuan</option>
                                <option value="pcs">Pcs (Piece)</option>
                                <option value="gr">Gr (Gram)</option>
                                <option value="ml">Ml (Mililiter)</option>
                                <option value="unit">Unit</option>
                            </select>
                            @error('form.unit')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </form>
                </div>

                {{-- Footer --}}
                <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-4 py-2.5 flex justify-end gap-2 rounded-b-2xl">
                    <button type="button" wire:click="closeModalAdd"
                        class="px-4 py-1 cursor-pointer bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="button" wire:click="saveItem"
                        class="px-4 py-1 cursor-pointer bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                        Tambah Barang
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Edit Item Modal --}}
    @if ($showEdit && $itemDetailById)
        <div x-data x-show="true" x-transition.opacity @click.self="$wire.closeModalEdit()"
            class="fixed inset-0 bg-black/30 flex items-center justify-center p-4 z-50">

            <div @click.stop class="bg-white no-scrollbar rounded-2xl max-w-3xl w-full xs:w-sm lg:w-3xl max-h-[90vh] overflow-y-auto shadow-xl">
                
                {{-- Header --}}
                <div class="sticky top-0 bg-white border-b border-gray-200 px-4 pt-3 pb-2 flex items-center justify-between rounded-t-2xl">
                    <h2 class="text-lg font-medium">Edit Barang</h2>
                    <button type="button" wire:click="closeModalEdit"
                        class="cursor-pointer text-gray-400 hover:text-gray-600">
                        <i class="ph ph-x text-2xl"></i>
                    </button>
                </div>

                {{-- Form Content --}}
                <div class="p-6">
                    <form wire:submit="updateItem" class="space-y-4">
                        
                        {{-- Current Image --}}
                        @if($itemDetailById->image)
                            <div class="w-full">
                                <label class="text-sm font-medium text-gray-700 mb-2 block">Gambar Saat Ini</label>
                                <div class="w-full aspect-[4/3] lg:max-w-xs bg-gray-200 rounded-xl overflow-hidden">
                                    <img src="{{ $itemDetailById->image }}" class="w-full h-full object-cover" alt="{{ $itemDetailById->name }}">
                                </div>
                            </div>
                        @endif

                        {{-- Image Upload --}}
                        <div class="w-full">
                            <label class="text-sm font-medium text-gray-700 mb-2 block">
                                Upload Gambar Baru (Opsional)
                            </label>
                            
                            <div x-data="{ 
                                isDragging: false,
                                handleDrop(e) {
                                    this.isDragging = false;
                                    const files = e.dataTransfer.files;
                                    if (files.length > 0) {
                                        @this.upload('newImage', files[0]);
                                    }
                                }
                            }"
                                @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="handleDrop"
                                :class="isDragging ? 'border-primary bg-primary/5' : 'border-gray-300'"
                                class="relative border-2 border-dashed rounded-lg p-6 transition-colors">
                                
                                <div class="text-center">
                                    <i class="ph ph-upload-simple text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-600 mb-2">
                                        Drag & drop gambar di sini atau
                                    </p>
                                    <label class="cursor-pointer inline-block px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                                        <span>Pilih File</span>
                                        <input type="file" wire:model="newImage" accept="image/*" class="hidden">
                                    </label>
                                    <p class="text-xs text-gray-500 mt-2">PNG, JPG, GIF hingga 2MB</p>
                                </div>
                            </div>
                            
                            @error('newImage')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            
                            {{-- Preview new upload --}}
                            <div wire:loading wire:target="newImage" class="text-sm text-gray-500 mt-2 flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Uploading...
                            </div>
                            @if($newImage)
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600 mb-2">Preview Gambar Baru:</p>
                                    <div class="w-full aspect-[4/3] lg:max-w-xs bg-gray-200 rounded-xl overflow-hidden relative">
                                        <img src="{{ $newImage->temporaryUrl() }}" class="w-full h-full object-cover" alt="Preview">
                                        <button type="button" wire:click="$set('newImage', null)" 
                                            class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700">
                                            <i class="ph ph-x text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Name --}}
                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-2 block">Nama Barang *</label>
                            <input type="text" wire:model="form.name" placeholder="Contoh: Tepung Terigu"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition">
                            @error('form.name')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Type --}}
                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-2 block">Kategori *</label>
                            <select wire:model="form.type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition">
                                <option value="">Pilih Kategori</option>
                                <option value="BAHAN_MENTAH">Bahan Mentah</option>
                                <option value="BAHAN_PENUNJANG">Bahan Penunjang</option>
                                <option value="KEMASAN">Kemasan</option>
                            </select>
                            @error('form.type')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Cost --}}
                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-2 block">Harga Satuan (Rp) *</label>
                            <input type="number" wire:model="form.cost" min="0" placeholder="Contoh: 15000"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition">
                            @error('form.cost')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Unit --}}
                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-2 block">Satuan *</label>
                            <select wire:model="form.unit"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition">
                                <option value="">Pilih Satuan</option>
                                <option value="pcs">Pcs (Piece)</option>
                                <option value="gr">Gr (Gram)</option>
                                <option value="ml">Ml (Mililiter)</option>
                                <option value="unit">Unit</option>
                            </select>
                            @error('form.unit')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </form>
                </div>

                {{-- Footer --}}
                <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-4 py-2.5 flex justify-end gap-2 rounded-b-2xl">
                    <button type="button" wire:click="closeModalEdit"
                        class="px-4 py-1 cursor-pointer bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="button" wire:click="updateItem"
                        class="px-4 py-1 cursor-pointer bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    @endif


</div>
