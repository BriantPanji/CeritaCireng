<?php

use App\Livewire\Forms\ItemForm;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use App\Models\Item;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

new #[Layout('components.layouts.app'), Title('Inventaris / Gudang')] class extends Component {
    use WithPagination;
    use WithFileUploads;

    public ItemForm $createForm;
    public ItemForm $editForm;

    public $query = '';
    public $itemDetailById = null;
    public $showModal = false;
    public $showModalDelete = false;
    public $showModalDeleteDone = false;
    public $addModal = false;
    public $editModal = false;
    public $editItemById = null;

    public function updatedQuery()
    {
        $this->resetPage();
    }

    public function simpan()
    {
        $this->dispatch('confirm-simpan', ...[
            'title' => 'Konfirmasi Simpan',
            'text' => 'Apakah Anda yakin ingin menyimpan data ini?',
            'icon' => 'warning',
            'confirmButtonText' => 'Ya, Simpan',
            'cancelButtonText' => 'Batal',
        ]);
    }

    public function simpanEdit()
    {
        $this->dispatch('confirm-edit', ...[
            'title' => 'Konfirmasi Simpan Edit',
            'text' => 'Apakah Anda yakin ingin mengedit  data ini?',
            'icon' => 'warning',
            'confirmButtonText' => 'Ya, Simpan Edit',
            'cancelButtonText' => 'Batal',
        ]);
    }

    #[On('storeCreateForm')]
    public function storeCreateForm()
    {
        $this->createForm->store();
        return redirect()->to(request()->header('Referer'));
    }

    #[On('storeEditform')]
    public function storeEditform()
    {
        $this->editForm->update();
        return redirect()->to(request()->header('Referer'));
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

    public function showAddModal()
    {
        $this->addModal = true;
        $this->dispatch('modal-add-opened');
    }

    public function closeAddModal()
    {
        $this->addModal = false;
        $this->dispatch('modal-add-closed');
    }

    public function showEditModal($id)
    {
        $this->editItemById = Item::find($id);
        $this->editForm->setItem(Item::find($id));
        $this->closeModal();
        $this->editModal = true;
        $this->dispatch('modal-edit-opened');
    }

    public function closeEditModal($id)
    {
        $this->editItemById = null;
        $this->editModal = false;
        $this->dispatch('modal-edit-closed');
        $this->showDetail($id);
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

    public function deletePreview()
    {
        $this->createForm->imageFile = null;
    }
}; ?>

<div class="min-w-full max-w-full"
     x-data
     @modal-opened.window="document.body.style.overflow = 'hidden'"
     @modal-closed.window="document.body.style.overflow = 'auto'"
     @modal-edit-opened.window="document.body.style.overflow = 'hidden'"
     @modal-edit-closed.window="document.body.style.overflow = 'auto'"
     @modal-add-opened.window="document.body.style.overflow = 'hidden'"
     @modal-add-closed.window="document.body.style.overflow = 'auto'">
    <section class="min-w-full max-w-full h-fit mb-4 flex gap-2 items-center">
        <div x-data="{xShow: false}" class="w-full max-w-full max-h-full group relative">
            <span onclick="document.getElementById('cari').focus()"
                  class="max-h-full min-h-full w-9 absolute flex cursor-text items-center justify-center">
                <i class="ph ph-magnifying-glass"></i>
            </span>
            <input wire:model.live.debounce.300ms="query" @input.debounce.250ms="xShow = $event.target.value.length > 0"
                   type="text" role="searchbox" name="cari" id="cari"
                   autocomplete="off" placeholder="Cari barang..."
                   class="w-full px-9 p-1 max-h-10 h-10 text-xs ring-[0.5px] ring-gray-400 rounded-xl outline-none group-hover:ring-primary group-hover:ring-[0.5px] focus:ring-primary-200 focus:ring-[0.5px]"/>
            <button type="button" x-show="xShow" onclick="document.getElementById('cari').focus();"
                    @click="xShow = false" wire:click="$set('query', '')"
                    class="absolute right-0 top-0 max-h-full min-h-full w-9 flex items-center justify-center cursor-pointer">
                <i class="ph ph-x text-lg"></i>
            </button>
        </div>
        <div class="w-max max-w-full min-h-full">
            <button type="button" wire:click="showAddModal"
                    class="px-4 py-2 w-max hidden sm:flex h-full max-w-full bg-primary text-white rounded-lg hover:bg-primary/90 transition cursor-pointer">
                Tambah Barang
            </button>
            <button type="button" wire:click="showAddModal"
                    class="px-4 py-2 flex sm:hidden items-center justify-center w-max min-h-full max-w-full bg-primary text-white rounded-lg hover:bg-primary/90 transition cursor-pointer">
                <i class="ph ph-plus text-xl h-full"></i>
            </button>
        </div>
    </section>

    <section class="min-w-full max-w-full min-h-[75vh] flex flex-wrap gap-4">
        @forelse ($items as $item)
            <article wire:key="item-{{ $item->id }}"
                     class="min-w-full max-w-full min-h-10 xs:!max-w-[47%] xs:min-w-[47%] lg:!max-w-[31%] lg:min-w-[31%] h-fit bg-white rounded-xl">
                <div wire:dblclick="showDetail({{ $item->id }})"
                     class="min-w-full max-w-full aspect-[4/3] bg-gray-400 rounded-t-xl object-cover">
                    @if(\Str::startsWith($item->image, 'https://'))
                        <img src="{{ $item->image }}"
                             class="max-h-full min-w-full aspect-[4/3] object-cover rounded-t-xl"
                             alt="{{ $item->name }}">
                    @else
                        <img src="{{ asset('storage/' . $item->image) }}"
                             class="max-h-full min-w-full aspect-[4/3] object-cover rounded-t-xl"
                             alt="{{ $item->name }}">
                    @endif
                </div>
                <div class="min-h-22 max-h-full min-w-full max-w-full p-4 flex flex-col justify-between">
                    <h3 class="text-lg xs:text-base font w-fit max-w-full truncate cursor-pointer"
                        wire:click="showDetail({{ $item->id }})">{{ \Str::title($item->name) }}</h3>
                    <span class="text-sm min-w-full max-w-full flex items-center justify-between">
                        <span class="font-light xs:text-xs text-neutral-400">Jumlah: {{ $item->stock->stock }}
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

    @if($addModal)
        <div class="fixed inset-0  bg-black/30 flex items-center justify-center p-4 z-50">
            <form wire:submit.prevent="simpan">
                <div @click.stop
                     class="bg-white max-h-[80vh] rounded-2xl max-w-3xl w-full xs:w-sm lg:w-3xl overflow-y-auto shadow-xl">
                    <div
                        class="sticky top-0 bg-white border-b border-gray-200 px-4 pt-3 pb-2 flex items-center justify-between rounded-t-2xl">
                        <h2 class="text-lg font-medium">Tambah Barang</h2>
                        <button type="button" wire:click="closeAddModal" title="Tutup Popup"
                                class="cursor-pointer text-gray-400 hover:text-gray-600">
                            <i class="ph ph-x text-2xl"></i>
                        </button>
                    </div>

                    <div class="py-3 px-4 flex flex-col lg:flex-row gap-3">

                        <div class="flex flex-col gap-4 w-full">
                            <div class="flex flex-col gap-1">
                                <label for="nama_barang" class="select-none">Nama Barang:</label>
                                <input wire:model="createForm.name" type="text" id="nama_barang"
                                       class="w-full p-2 ring outline-none focus:ring-gray-600 ring-gray-300 rounded-md"
                                       placeholder="Ayam pedas...">
                                @error('createForm.name') <span
                                    class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex flex-col gap-1">
                                <label for="harga_barang" class="select-none">Harga Barang (Rp):</label>
                                <input wire:model="createForm.cost" type="number" min="0" id="harga_barang" required
                                       class="w-full p-2 ring outline-none focus:ring-gray-600 ring-gray-300 rounded-md"
                                       placeholder="20">
                                @error('createForm.cost') <span
                                    class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>


                            <div class="flex flex-col gap-1">
                                <label for="unit_barang" class="select-none">Unit:</label>
                                <select wire:model="createForm.unit" id="unit_barang"
                                        class="w-full p-2 ring outline-none focus:ring-gray-600 ring-gray-300 rounded-md">
                                    <option value="pcs">pcs</option>
                                    <option value="gr">gr</option>
                                    <option value="ml">ml</option>
                                    <option value="unit">unit</option>
                                </select>
                                @error('createForm.unit') <span
                                    class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex flex-col gap-1">
                                <label for="type_barang" class="select-none">Kategori:</label>
                                <select wire:model="createForm.type" id="type_barang"
                                        class="w-full p-2 ring outline-none focus:ring-gray-600 ring-gray-300 rounded-md">
                                    <option disabled selected value="DEFAULT">Pilih Kategori</option>
                                    <option value="BAHAN_MENTAH">Bahan Mentah</option>
                                    <option value="BAHAN_PENUNJANG">Bahan Penunjang</option>
                                    <option value="KEMASAN">Kemasan</option>
                                </select>
                                @error('createForm.type') <span
                                    class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        <div class="w-full h-full flex flex-col gap-3 lg:min-w-2/5 lg:max-w-2/5">
                            <div class="flex flex-col gap-1">
                                <label for="gambar" class="select-none">Gambar:</label>
                                <input type="file" wire:model="createForm.imageFile" wire:click="deletePreview"
                                       id="gambar" accept="image/*"
                                       class="w-full p-2 ring file:bg-primary file:min-h-full file:max-h-full file:px-2 file:text-base cursor-pointer file:cursor-pointer file:rounded-sm outline-none focus:ring-gray-600 ring-gray-300 rounded-md"/>
                                @error('createForm.imageFile') <span
                                    class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>
                            <div class="w-full h-full flex flex-col">
                                <span>Preview:</span>
                                <div
                                    class="flex items-center justify-center aspect-[4/3] mb-4 rounded-xl *:rounded-xl overflow-hidden">
                                    @if($createForm->imageFile)
                                        @if($errors->has('image'))
                                            <img src="https://placehold.co/600x400.webp?text=Tidak%20Valid"
                                                 alt="Preview"
                                                 class="w-full object-cover aspect-[4/3]"/>
                                        @else
                                            <img src="{{ $createForm->imageFile->temporaryUrl() }}" alt="Preview"
                                                 class="w-full object-cover aspect-[4/3]"/>
                                        @endif
                                    @else
                                        <img src="https://placehold.co/600x400.webp?text=Foto+Item"
                                             alt="Placeholder Image" title="Klik untuk menambahkan gambar"
                                             class="w-full object-cover aspect-[4/3]"
                                             onclick="document.getElementById('gambar').click()"/>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-4 py-2.5 flex justify-end gap-2 rounded-b-2xl">
                        <button autofocus type="reset"
                                class="px-4 py-1 cursor-pointer focus:shadow-[0_0_0_2px_#111] bg-white border border-gray-300 text-black rounded-lg hover:bg-gray-300 transition">
                            Reset
                        </button>
                        <button autofocus type="submit"
                                class="px-4 py-1 cursor-pointer focus:shadow-[0_0_0_2px_#111] bg-primary border border-gray-300 text-black rounded-lg hover:bg-primary/75 transition">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @endif
    @if($editModal && $editItemById)
        <div class="fixed inset-0  bg-black/30 flex items-center justify-center p-4 z-50">
            <form wire:submit.prevent="simpanEdit">
                <div @click.stop
                     class="bg-white max-h-[80vh] rounded-2xl max-w-3xl w-full xs:w-sm lg:w-3xl overflow-y-auto shadow-xl">
                    <div
                        class="sticky top-0 bg-white border-b border-gray-200 px-4 pt-3 pb-2 flex items-center justify-between rounded-t-2xl">
                        <h2 class="text-lg font-medium">Edit Barang</h2>
                        <button type="button" wire:click="closeEditModal({{$editItemById->id}})" title="Tutup Popup"
                                class="cursor-pointer text-gray-400 hover:text-gray-600">
                            <i class="ph ph-x text-2xl"></i>
                        </button>
                    </div>

                    <div class="py-3 px-4 flex flex-col lg:flex-row gap-3">

                        <div class="flex flex-col gap-4 w-full">
                            <div class="flex flex-col gap-1">
                                <label for="nama_barang" class="select-none">Nama Barang:</label>
                                <input wire:model="editForm.name" type="text" id="nama_barang"
                                       class="w-full p-2 ring outline-none focus:ring-gray-600 ring-gray-300 rounded-md"
                                       placeholder="Ayam pedas...">
                                @error('editForm.name') <span
                                    class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex flex-col gap-1">
                                <label for="harga_barang" class="select-none">Harga Barang (Rp):</label>
                                <input wire:model="editForm.cost" type="number" min="0" id="harga_barang" required
                                       class="w-full p-2 ring outline-none focus:ring-gray-600 ring-gray-300 rounded-md"
                                       placeholder="20">
                                @error('editForm.cost') <span
                                    class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>


                            <div class="flex flex-col gap-1">
                                <label for="unit_barang" class="select-none">Unit:</label>
                                <select wire:model="editForm.unit" id="unit_barang"
                                        class="w-full p-2 ring outline-none focus:ring-gray-600 ring-gray-300 rounded-md">
                                    <option value="pcs">pcs</option>
                                    <option value="gr">gr</option>
                                    <option value="ml">ml</option>
                                    <option value="unit">unit</option>
                                </select>
                                @error('editForm.unit') <span
                                    class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex flex-col gap-1">
                                <label for="type_barang" class="select-none">Kategori:</label>
                                <select wire:model="editForm.type" id="type_barang"
                                        class="w-full p-2 ring outline-none focus:ring-gray-600 ring-gray-300 rounded-md">
                                    <option disabled selected value="DEFAULT">Pilih Kategori</option>
                                    <option value="BAHAN_MENTAH">Bahan Mentah</option>
                                    <option value="BAHAN_PENUNJANG">Bahan Penunjang</option>
                                    <option value="KEMASAN">Kemasan</option>
                                </select>
                                @error('editForm.type') <span
                                    class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        <div class="w-full h-full flex flex-col gap-3 lg:min-w-2/5 lg:max-w-2/5">
                            <div class="flex flex-col gap-1">
                                <label for="gambar" class="select-none">Gambar:</label>
                                <input type="file" wire:model="editForm.imageFile" wire:click="deletePreview"
                                       id="gambar" accept="image/*"
                                       class="w-full p-2 ring file:bg-primary file:min-h-full file:max-h-full file:px-2 file:text-base cursor-pointer file:cursor-pointer file:rounded-sm outline-none focus:ring-gray-600 ring-gray-300 rounded-md"/>
                                @error('editForm.imageFile') <span
                                    class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>
                            <div class="w-full h-full flex flex-col">
                                <span>Preview:</span>
                                <div
                                    class="flex items-center justify-center aspect-[4/3] mb-4 rounded-xl *:rounded-xl overflow-hidden">
                                    @if($editForm->imageFile)
                                        @if($errors->has('image'))
                                            <img src="https://placehold.co/600x400.webp?text=Tidak%20Valid"
                                                 alt="Preview"
                                                 class="w-full object-cover aspect-[4/3]"/>
                                        @else
                                            <img src="{{ $editForm->imageFile->temporaryUrl() }}" alt="Preview"
                                                 class="w-full object-cover aspect-[4/3]"/>
                                        @endif
                                    @elseif($editForm->image)
                                        @if(\Str::startsWith($editForm->image, 'https://'))
                                            <img src="{{ $editForm->image }}"
                                                 class="w-full object-cover aspect-[4/3]"
                                                 alt="Preview">
                                        @else
                                            <img src="{{ asset('storage/' . $editForm->image) }}"
                                                 class="w-full object-cover aspect-[4/3]"
                                                 alt="Preview">
                                        @endif
                                    @else
                                        <img src="https://placehold.co/600x400.webp?text=Foto+Item"
                                             alt="Placeholder Image" title="Klik untuk menambahkan gambar"
                                             class="w-full object-cover aspect-[4/3]"
                                             onclick="document.getElementById('gambar').click()"/>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-4 py-2.5 flex justify-end gap-2 rounded-b-2xl">
                        <button autofocus type="reset"
                                class="px-4 py-1 cursor-pointer focus:shadow-[0_0_0_2px_#111] bg-white border border-gray-300 text-black rounded-lg hover:bg-gray-300 transition">
                            Reset
                        </button>
                        <button autofocus type="submit"
                                class="px-4 py-1 cursor-pointer focus:shadow-[0_0_0_2px_#111] bg-primary border border-gray-300 text-black rounded-lg hover:bg-primary/75 transition">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @endif

    {{-- Modal Detail --}}
    @if ($showModal && $itemDetailById)
        <div x-data x-show="true" x-transition.opacity @click.self="$wire.closeModal()"
             class="fixed inset-0  bg-black/30 flex items-center justify-center p-4 z-50">

            <div @click.stop
                 class="bg-white no-scrollbar rounded-2xl max-w-3xl w-full xs:w-sm lg:w-3xl max-h-[90vh] overflow-y-auto shadow-xl">

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
                        @if(\Str::startsWith($itemDetailById->image, 'https://'))
                            <img src="{{ $itemDetailById->image }}"
                                 class="max-h-full min-w-full aspect-[4/3] object-cover rounded-t-xl"
                                 alt="{{ $itemDetailById->name }}">
                        @else
                            <img src="{{ asset('storage/' . $itemDetailById->image) }}"
                                 class="max-h-full min-w-full aspect-[4/3] object-cover rounded-t-xl"
                                 alt="{{ $itemDetailById->name }}">
                        @endif
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
                                    <td class="py-2 lg:py-1.5 font-medium text-right w-[59%]">{{ $itemDetailById->stock->stock }}
                                        {{ ($itemDetailById->unit) }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="py-2 lg:py-1.5 text-gray-600 w-[40%]">Harga Satuan</td>
                                    <td class="py-2 lg:py-1.5 w-[1%]">:</td>
                                    <td class="py-2 lg:py-1.5 font-medium text-right w-[59%]">@convertRupiah($itemDetailById->cost)
                                        /
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
                    <button type="button" wire:click="showEditModal({{ $itemDetailById->id }})"
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
    @script
    <script>
        document.addEventListener('confirm-simpan', event => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.text,
                icon: event.detail.icon,
                showCancelButton: true,
                confirmButtonText: event.detail.confirmButtonText,
                cancelButtonText: event.detail.cancelButtonText,
                confirmButtonColor: "#efa800",
                cancelButtonColor: "#990000"
            }).then(result => {
                if (result.isConfirmed) {
                    // console.log($wire);
                    $wire.dispatch('storeCreateForm');
                }
            });
        });
        document.addEventListener('confirm-edit', event => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.text,
                icon: event.detail.icon,
                showCancelButton: true,
                confirmButtonText: event.detail.confirmButtonText,
                cancelButtonText: event.detail.cancelButtonText,
                confirmButtonColor: "#efa800",
                cancelButtonColor: "#990000"
            }).then(result => {
                if (result.isConfirmed) {
                    $wire.dispatch('storeEditform');
                }
            });
        });
    </script>
    @endscript

</div>
