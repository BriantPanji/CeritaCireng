<?php

use Livewire\Volt\Component;
use App\Models\Outlet;
use App\Models\User;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    // Filter & search
    public $search = '';
    public $status = ''; // AKTIF / NONAKTIF / ''

    // Selection
    public $selectedOutlets = [];
    public $selectAll = false;

    // Modal tambah outlet
    public $showAddModal = false;

    // Modal detail staff
    public $showDetailModal = false;
    public $detailOutlet = null;

    // Form fields
    public $name;
    public $location;
    public $statusForm = 'AKTIF';
    public $staff_users = null ;

    protected $updatesQueryString = ['status' => ['except' => '']];

    // reset pagination when filters change
    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }

    // =======================
    // Query Outlets
    // =======================
    protected function getOutlets()
    {
        $query = Outlet::query()->withCount('users');

        if ($this->search !== '') {
            $query->where('name', 'like', "%{$this->search}%")
                  ->orWhere('location', 'like', "%{$this->search}%");
        } elseif ($this->status !== '') {
            $query->where('status', $this->status);
        }

        return $query->orderBy('created_at', 'desc')->paginate(8)->withQueryString();
    }

    // =======================
    // SELECT ALL
    // =======================
    public function toggleSelectAll()
    {
        $this->selectAll = ! $this->selectAll;

        if ($this->selectAll) {
            $outlets = $this->getOutlets();
            $this->selectedOutlets = $outlets->pluck('id')->toArray();
        } else {
            $this->selectedOutlets = [];
        }
    }

    public function toggleOutlet($id)
    {
        if (in_array($id, $this->selectedOutlets)) {
            $this->selectedOutlets = array_diff($this->selectedOutlets, [$id]);
        } else {
            $this->selectedOutlets[] = $id;
        }

        $outlets = $this->getOutlets();
        $this->selectAll = count($this->selectedOutlets) === $outlets->total();
    }

    // =======================
    // Mass actions (activate / deactivate)
    // =======================
    public function deactivateSelected()
    {
        if (empty($this->selectedOutlets)) return;

        Outlet::whereIn('id', $this->selectedOutlets)->update(['status' => 'NONAKTIF']);

        $this->selectedOutlets = [];
        $this->selectAll = false;

        session()->flash('success', 'Outlet berhasil dinonaktifkan.');
    }

    public function activateSelected()
    {
        if (empty($this->selectedOutlets)) return;

        Outlet::whereIn('id', $this->selectedOutlets)->update(['status' => 'AKTIF']);

        $this->selectedOutlets = [];
        $this->selectAll = false;

        session()->flash('success', 'Outlet berhasil diaktifkan.');
    }

    public function deleteOutlet()
    {
        if (empty($this->selectedOutlets)) return;

        User::where('outlet_id', $this->selectedOutlets)->update(['outlet_id' => null]);
        Outlet::whereIn('id', $this->selectedOutlets)->delete();

        $this->selectedOutlets = [];
        $this->selectAll = false;

        session()->flash('success', 'Outlet berhasil dihapus.');
    }


    // =======================
    // ADD OUTLET
    // =======================
    public function openAddModal()
    {
        $this->resetForm();
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
    }

    public function resetForm()
    {
        $this->name = '';
        $this->location = '';
        $this->statusForm = 'AKTIF';
        $this->staff_users = [];
    }

    public function saveOutlet()
    {
        $this->validate([
            'name' => 'required|string|max:128',
            'location' => 'required|string|max:2048',
            'statusForm' => 'required|in:AKTIF,NONAKTIF',
            'staff_users' => 'array',
            'staff_users.*' => 'exists:users,id',
        ]);

        $outlet = Outlet::create([
            'name' => $this->name,
            'location' => $this->location,
            'status' => $this->statusForm,
        ]);

        // Assign selected users to this outlet
        if (!empty($this->staff_users)) {
            User::whereIn('id', $this->staff_users)
                ->update(['outlet_id' => $outlet->id]);
        }

        $this->resetForm();
        $this->showAddModal = false;

        session()->flash('success', 'Outlet berhasil ditambahkan dan staff diassign.');
        return redirect()->route('outlets.management');
    }

    // =======================
    // DETAIL: show staff list
    // =======================
    public function openDetail($id)
    {
        $this->detailOutlet = Outlet::with('users')->find($id);
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->detailOutlet = null;
    }

    public function with()
    {
        return [
            'outlets' => $this->getOutlets(),
            'allUsers' => User::orderBy('display_name')->get(),
        ];
    }
};
?>

<div class="p-4 space-y-4">
    <!-- Search + Status Filter -->
    <div class="flex items-center justify-between">
        <div class="flex items-center bg-white p-2 rounded-lg w-full mr-3 shadow-sm border hover:border-primary cursor-pointer">
            <i class="ph ph-magnifying-glass text-gray-400 text-base"></i>
            <input type="text"
                   wire:model.debounce.300ms="search"
                   wire:keydown.enter="$refresh"
                   placeholder="Cari outlet (nama atau lokasi)"
                   class="ml-2 w-full outline-none text-sm">
        </div>

        <div class="relative" x-data="{ open:false }">
            <button @click="open = !open"
                    class="bg-primary text-white px-4 py-1 rounded-lg shadow text-sm flex items-center gap-1 cursor-pointer">
                Status <i class="ph ph-funnel-simple ml-1"></i>
            </button>

            <div x-show="open" @click.outside="open = false"
                 class="absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-44 text-sm z-50">
                <button wire:click="$set('status', '')" class="px-4 py-2 w-full text-left hover:bg-gray-200 border-b cursor-pointer">Semua Status</button>
                <button wire:click="$set('status', 'AKTIF')" class="px-4 py-2 w-full text-left hover:bg-gray-200 border-b cursor-pointer">AKTIF</button>
                <button wire:click="$set('status', 'NONAKTIF')" class="px-4 py-2 w-full text-left hover:bg-gray-200 cursor-pointer">NONAKTIF</button>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-center flex-wrap sm:flex-nowrap gap-4">
        <h2 class="font-semibold text-base">
            Semua Outlet <span class="text-gray-400">{{ $outlets->total() }}</span>
        </h2>

        <button wire:click="openAddModal"
                class="flex items-center gap-1 bg-white px-3 py-1 rounded-lg shadow text-sm border border-black cursor-pointer">
            Tambah Outlet
            <i class="ph ph-plus text-sm"></i>
        </button>
    </div>

    <!-- LIST OUTLETS -->
    <div class="bg-white rounded-lg shadow p-3 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <!-- Header -->
            <thead class="bg-gray-50">
                <tr class="text-left text-sm font-semibold text-gray-700">
                    <th class="px-3 py-2">
                        <input type="checkbox" class="cursor-pointer"{{ $selectAll ? 'checked' : '' }}
                            wire:click="toggleSelectAll">
                    </th>
                    <th class="px-3 py-2 cursor-pointer select-none">Nama Outlet</th>
                    <th class="px-3 py-2">Location</th>
                    <th class="px-3 py-2 text-center">Status</th>
                    <th class="px-3 py-2 text-center">Staff</th>
                    <th class="px-3 py-2 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 cursor-pointer">
                @forelse ($outlets as $outlet)
                    <tr class="hover:bg-gray-100 cursor-pointer" wire:key="outlet-{{ $outlet->id }}">
                        <!-- Checkbox -->
                        <td class="px-3 py-2">
                            <input type="checkbox" 
                                wire:click.stop="toggleOutlet({{ $outlet->id }})"
                                {{ in_array($outlet->id, $selectedOutlets) ? 'checked' : '' }}>
                        </td>

                        <!-- Nama -->
                        <td class="px-3 py-2 cursor-pointer select-none" wire:click.stop="toggleOutlet({{ $outlet->id }})">
                            {{ $outlet->name }}
                        </td>

                        <!-- Location -->
                        <td class="px-3 py-2" wire:click.stop="toggleOutlet({{ $outlet->id }})">
                            {{ Str::limit($outlet->location, 60) }}
                        </td>

                        <!-- Status -->
                        <td class="px-3 py-2 align-middle text-center">
                            <span class="px-2 py-0.5 text-xs rounded-full bg-transparent border
                                {{ $outlet->status === 'AKTIF'
                                    ? 'border-blue-700 text-blue-700'
                                    : 'border-red-700 text-red-700' }}" wire:click.stop="toggleOutlet({{ $outlet->id }})">
                                {{ $outlet->status }}
                            </span>
                        </td>

                        <!-- Staff Count -->
                        <td class="px-3 py-2 align-middle text-center" wire:click.stop="toggleOutlet({{ $outlet->id }})">
                            {{ $outlet->users_count }}
                        </td>

                        <!-- Aksi -->
                        <td class="px-3 py-2 align-middle text-center cursor-pointer" wire:click.stop="toggleOutlet({{ $outlet->id }})">
                            <button class="bg-primary text-white px-3 py-1 rounded-lg text-xs shadow cursor-pointer"
                                    wire:click.stop="openDetail({{ $outlet->id }})">
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-4 text-sm">
                            Tidak ada outlet ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex justify-end gap-2 mt-3">
        <button wire:click.stop="deleteOutlet({{ $outlet->id }})"
            class="bg-neutral-200 text-white px-4 py-1 rounded-lg shadow text-sm disabled:opacity-50 w-full sm:w-auto cursor-pointer"
            @disabled(empty($selectedOutlets))>
            Hapus
        </button>
        <button wire:click="deactivateSelected"
            class="bg-red-700 text-white px-4 py-1 rounded-lg shadow text-sm disabled:opacity-50 w-full sm:w-auto cursor-pointer"
            @disabled(empty($selectedOutlets))>
            Nonaktif
        </button>
        <button wire:click="activateSelected"
            class="bg-blue-700 text-white px-4 py-1 rounded-lg shadow text-sm disabled:opacity-50 w-full sm:w-auto cursor-pointer"
            @disabled(empty($selectedOutlets))>
            Aktif
        </button>
    </div>

    <!-- Pagination -->
    <div class="w-full mt-4">
        {{ $outlets->links('vendor.pagination.custom') }}
    </div>

    <!-- ADD OUTLET MODAL -->
    @if($showAddModal)
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 overflow-y-auto no-scrollbar" style="z-index: 999;"
         wire:click="closeAddModal">
        <div class="min-h-full flex justify-center items-center py-8 px-4" wire:click.stop>
            <div class="bg-white w-full max-w-xl rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold mb-4">Tambah Outlet</h2>

                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium">Nama Outlet</label>
                        <input type="text" wire:model="name"
                            class="w-full border p-2 border-neutral-300 rounded-lg" placeholder="Masukkan nama outlet">
                        @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Location</label>
                        <textarea wire:model="location" class="w-full border p-2 border-neutral-300 rounded-lg" rows="3" placeholder="Masukkan lokasi atau alamat lengkap"></textarea>
                        @error('location') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Status</label>
                        <select wire:model="statusForm" class="w-full border p-2 rounded-lg border-neutral-300">
                            <option value="AKTIF">AKTIF</option>
                            <option value="NONAKTIF">NONAKTIF</option>
                        </select>
                        @error('statusForm') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="closeAddModal" class="px-4 py-2 rounded border border-neutral-300 bg-white hover:bg-neutral-200 cursor-pointer">
                        Batal
                    </button>

                    <button wire:click="saveOutlet" class="px-4 py-2 rounded bg-primary text-white cursor-pointer">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- DETAIL OUTLET MODAL (staff list) -->
    @if($showDetailModal && $detailOutlet)
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 overflow-y-auto no-scrollbar" style="z-index: 999;"
         wire:click="closeDetail">
        <div class="min-h-full flex justify-center items-center py-8 px-4" wire:click.stop>
            <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold mb-4">Staff - {{ $detailOutlet->name }}</h2>

                <div class="space-y-3">
                    <p class="text-sm text-gray-600">Location: {{ $detailOutlet->location }}</p>
                    <p class="text-sm text-gray-600">Status: {{ $detailOutlet->status }}</p>

                    <div class="mt-3">
                        <h3 class="font-medium">Daftar Staff ({{ $detailOutlet->users->count() }})</h3>
                        @if($detailOutlet->users->isEmpty())
                            <p class="text-sm text-gray-500 mt-2">Belum ada staff yang diassign ke outlet ini.</p>
                        @else
                            <ul class="mt-2 space-y-2">
                                @foreach($detailOutlet->users as $u)
                                    <li class="flex items-center justify-between">
                                        <div>
                                            <div class="font-medium">{{ $u->display_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $u->username }}</div>
                                        </div>
                                        <div class="text-xs text-gray-400">{{ $u->status ?? '' }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="closeDetail" class="px-4 py-2 rounded border border-neutral-100 bg-white hover:bg-neutral-100 cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
