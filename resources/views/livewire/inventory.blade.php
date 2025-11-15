<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use App\Models\Item;
use Livewire\WithPagination;

new #[Layout('components.layouts.app'), Title('Inventaris / Gudang')] class extends Component {
    use WithPagination;

    public $query = '';
    public $itemDetailById = null;
    public $showModal = false;
    public $showModalDelete = false;
    public $showModalDeleteDone = false;

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
        $this->js("document.body.style.overflow = 'hidden'");
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->itemDetailById = null;
        $this->js("document.body.style.overflow = 'auto'");
    }

    public function deleteItem($id)
    {
        $item = Item::find($id);
        if ($item) {
            $item->delete();
        }
        $this->showModalDelete = false;
        $this->showModalDeleteDone = true;
        $this->closeModal();
        $this->js("document.body.style.overflow = 'hidden'");
    }

    public function closeModalDeleteDone()
    {
        $this->showModalDeleteDone = false;
        $this->js("document.body.style.overflow = 'auto'");
    }
}; ?>

<div class="min-w-full max-w-full">
    <section class="min-w-full max-w-full h-fit mb-4">
        <div x-data="{xShow: false}" class="min-w-full max-w-full max-h-full group relative">
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
    </section>

    <section class="min-w-full max-w-full min-h-[75vh] flex flex-wrap gap-4">
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
                        <span class="font-light xs:text-xs text-neutral-400">Jumlah: {{ $item->stock->stock }}
                            {{ \Str::title($item->unit) }}</span>
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
                                    <td class="py-2 lg:py-1.5 font-medium text-right w-[59%]">{{ $itemDetailById->stock->stock }}
                                        {{ \Str::title($itemDetailById->unit) }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="py-2 lg:py-1.5 text-gray-600 w-[40%]">Harga Satuan</td>
                                    <td class="py-2 lg:py-1.5 w-[1%]">:</td>
                                    <td class="py-2 lg:py-1.5 font-medium text-right w-[59%]">@convertRupiah($itemDetailById->cost) /
                                        {{ \Str::title($itemDetailById->unit) }}</td>
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
                    <button type="button"
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


</div>
