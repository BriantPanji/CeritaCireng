<?php

use Livewire\Volt\Component;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Day;
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

    // Modal edit outlet
    public $showEditModal = false;
    public $editingOutletId = null;

    // Modal detail staff
    public $showDetailModal = false;
    public $detailOutlet = null;

    // Form fields
    public $name;
    public $location;
    public $statusForm = 'AKTIF';
    public $staff_users = null;
    public $closed_days = []; // Array untuk menyimpan hari-hari tutup

    protected $updatesQueryString = ['status' => ['except' => '']];

    // reset pagination when filters change - LIVE SEARCH
    public function updatedSearch() { 
        $this->resetPage(); 
        $this->selectAll = false;
        $this->selectedOutlets = [];
    }
    
    public function updatedStatus() { 
        $this->resetPage(); 
        $this->selectAll = false;
        $this->selectedOutlets = [];
    }

    // =======================
    // Query Outlets - INDEPENDEN
    // =======================
    protected function getOutlets()
    {
        $query = Outlet::query()->withCount('users');

        // Search dan Status filter bekerja bersamaan (independen)
        if ($this->search !== '') {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('location', 'like', "%{$this->search}%");
            });
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        return $query->orderBy('created_at', 'desc')->paginate(5)->withQueryString();
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

        User::whereIn('outlet_id', $this->selectedOutlets)->update(['outlet_id' => null]);
        
        // Hapus juga relasi closed days
        \DB::table('outlet_closed_days')->whereIn('outlet_id', $this->selectedOutlets)->delete();
        
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
        $this->closed_days = []; // Reset hari tutup
        $this->editingOutletId = null;
    }

    public function saveOutlet()
    {
        $this->validate([
            'name' => 'required|string|max:128',
            'location' => 'required|string|max:2048',
            'statusForm' => 'required|in:AKTIF,NONAKTIF',
            'staff_users' => 'array',
            'staff_users.*' => 'exists:users,id',
            'closed_days' => 'array',
            'closed_days.*' => 'exists:days,id',
        ]);

        $outlet = Outlet::create([
            'name' => $this->name,
            'location' => $this->location,
            'status' => $this->statusForm,
        ]);

        // Assign hari tutup ke outlet
        if (!empty($this->closed_days)) {
            $outlet->closedDays()->attach($this->closed_days);
        }

        // Assign selected users to this outlet
        if (!empty($this->staff_users)) {
            User::whereIn('id', $this->staff_users)
                ->update(['outlet_id' => $outlet->id]);
        }

        $this->resetForm();
        $this->showAddModal = false;

        session()->flash('success', 'Outlet berhasil ditambahkan dengan hari tutup.');
        return redirect()->route('outlets.management');
    }

    // =======================
    // EDIT OUTLET
    // =======================
    public function openEditModal($id)
    {
        $outlet = Outlet::with('closedDays')->findOrFail($id);
        
        $this->editingOutletId = $outlet->id;
        $this->name = $outlet->name;
        $this->location = $outlet->location;
        $this->statusForm = $outlet->status;
        $this->closed_days = $outlet->closedDays->pluck('id')->toArray();
        
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function updateOutlet()
    {
        $this->validate([
            'name' => 'required|string|max:128',
            'location' => 'required|string|max:2048',
            'statusForm' => 'required|in:AKTIF,NONAKTIF',
            'closed_days' => 'array',
            'closed_days.*' => 'exists:days,id',
        ]);

        $outlet = Outlet::findOrFail($this->editingOutletId);

        $outlet->update([
            'name' => $this->name,
            'location' => $this->location,
            'status' => $this->statusForm,
        ]);

        // Sync hari tutup (hapus yang lama, tambah yang baru)
        $outlet->closedDays()->sync($this->closed_days);

        $this->resetForm();
        $this->showEditModal = false;

        session()->flash('success', 'Outlet berhasil diupdate.');
    }

    // =======================
    // DETAIL: show staff list
    // =======================
    public function openDetail($id)
    {
        $this->detailOutlet = Outlet::with(['users', 'closedDays'])->find($id);
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
            'allDays' => Day::orderBy('day_number')->get(),
        ];
    }
};
?>

<div class="px-3 pt-3 space-y-4">
    <!-- Search + Status Filter -->
    <div class="flex items-center justify-between">
        <div class="flex items-center bg-white p-2 rounded-lg w-full mr-3 shadow-sm border hover:border-primary">
            <i class="ph ph-magnifying-glass text-gray-400 text-base"></i>
            <input type="text" wire:model.live.debounce.150ms="search" placeholder="Cari outlet (nama atau lokasi)"
                class="ml-2 w-full outline-none text-sm" autocomplete="off">
        </div>

        <div class="relative" x-data="{ open:false }">
            <button @click="open = !open"
                class="bg-primary text-white px-4 py-1 rounded-lg shadow text-sm flex items-center gap-1 cursor-pointer">
                Status <i class="ph ph-funnel-simple ml-1"></i>
            </button>

            <div x-show="open" @click.outside="open = false"
                class="absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-44 text-sm z-50">
                <button wire:click="$set('status', '')"
                    class="px-4 py-2 w-full text-left hover:bg-gray-200 border-b cursor-pointer">Semua Status</button>
                <button wire:click="$set('status', 'AKTIF')"
                    class="px-4 py-2 w-full text-left hover:bg-gray-200 border-b cursor-pointer">AKTIF</button>
                <button wire:click="$set('status', 'NONAKTIF')"
                    class="px-4 py-2 w-full text-left hover:bg-gray-200 cursor-pointer">NONAKTIF</button>
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
                        <input type="checkbox" class="cursor-pointer" {{ $selectAll ? 'checked' : '' }}
                            wire:click="toggleSelectAll">
                    </th>
                    <th class="px-3 py-2 cursor-pointer select-none">Nama Outlet</th>
                    <th class="px-3 py-2">Location</th>
                    <th class="px-3 py-2 text-center">Status</th>
                    <th class="px-3 py-2 text-center">Staff</th>
                    <th class="px-3 py-2 text-center">Edit</th>
                    <th class="px-3 py-2 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 cursor-pointer">
                @forelse ($outlets as $outlet)
                <tr class="hover:bg-gray-100 cursor-pointer" wire:key="outlet-{{ $outlet->id }}">
                    <!-- Checkbox -->
                    <td class="px-3 py-2">
                        <input type="checkbox" wire:click.stop="toggleOutlet({{ $outlet->id }})" {{
                            in_array($outlet->id, $selectedOutlets) ? 'checked' : '' }}>
                    </td>

                    <!-- Nama -->
                    <td class="px-3 py-2 cursor-pointer select-none text-1 w-[300px]"
                        wire:click.stop="toggleOutlet({{ $outlet->id }})">
                        {{ $outlet->name }}
                    </td>

                    <!-- Location -->
                    <td class="px-3 py-2 text-1 w-[450px]" wire:click.stop="toggleOutlet({{ $outlet->id }})">
                        {{ Str::limit($outlet->location, 60) }}
                    </td>

                    <!-- Status -->
                    <td class="px-3 py-2 align-middle text-center text-1 w-[100px]">
                        <p class="px-1 py-0.5 text-xs rounded-full bg-gray-100 border
                                {{ $outlet->status === 'AKTIF'
                                    ? 'border-blue-700 text-blue-700'
                                    : 'border-red-700 text-red-700' }}" wire:click="toggleOutlet({{ $outlet->id }})">
                            {{ $outlet->status }}
                        </p>
                    </td>

                    <!-- Staff Count -->
                    <td class="px-3 py-2 align-middle text-center w-[100px]"
                        wire:click.stop="toggleOutlet({{ $outlet->id }})">
                        {{ $outlet->users_count }}
                    </td>

                    <!-- Edit Button -->
                    <td class="px-3 py-2 align-middle text-center w-[100px]">
                        <button
                            class="bg-green-600 text-white px-5 py-1 rounded-lg text-xs shadow cursor-pointer hover:bg-green-800"
                            wire:click.stop="openEditModal({{ $outlet->id }})">
                            Edit
                        </button>
                    </td>

                    <!-- Aksi Detail -->
                    <td class="px-3 py-2 align-middle text-center cursor-pointer w-[100px]">
                        <button class="bg-primary text-white px-3 py-1 rounded-lg text-xs shadow cursor-pointer"
                            wire:click.stop="openDetail({{ $outlet->id }})">
                            Detail
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-gray-500 py-4 text-sm">
                        Tidak ada outlet ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex justify-end gap-2 mt-3">
        <button wire:click.stop="deleteOutlet"
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
                        <input type="text" wire:model="name" class="w-full border p-2 border-neutral-300 rounded-lg"
                            placeholder="Masukkan nama outlet">
                        @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Location</label>
                        <textarea wire:model="location" class="w-full border p-2 border-neutral-300 rounded-lg" rows="3"
                            placeholder="Masukkan lokasi atau alamat lengkap"></textarea>
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

                    <!-- HARI TUTUP SECTION -->
                    <div>
                        <label class="text-sm font-medium block mb-2">Hari Tutup (Opsional)</label>
                        <div class="border border-neutral-300 rounded-lg p-3 space-y-2">
                            <p class="text-xs text-gray-500 mb-2">Pilih hari-hari ketika outlet ini tutup</p>

                            <div class="grid grid-cols-2 gap-2">
                                @foreach($allDays as $day)
                                <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                                    <input type="checkbox" wire:model="closed_days" value="{{ $day->id }}"
                                        class="rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                                    <span class="text-sm">{{ $day->name }}</span>
                                </label>
                                @endforeach
                            </div>

                            @if(!empty($closed_days))
                            <div class="mt-2 pt-2 border-t border-gray-200">
                                <p class="text-xs text-gray-600">
                                    <strong>Dipilih:</strong>
                                    {{ $allDays->whereIn('id', $closed_days)->pluck('name')->join(', ') }}
                                </p>
                            </div>
                            @endif
                        </div>
                        @error('closed_days') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="closeAddModal"
                        class="px-4 py-2 rounded border border-neutral-300 bg-white hover:bg-neutral-200 cursor-pointer">
                        Batal
                    </button>

                    <button wire:click="saveOutlet"
                        class="px-4 py-2 rounded bg-primary text-white cursor-pointer hover:bg-primary/90">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- EDIT OUTLET MODAL -->
    @if($showEditModal)
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 overflow-y-auto no-scrollbar" style="z-index: 999;"
        wire:click="closeEditModal">
        <div class="min-h-full flex justify-center items-center py-8 px-4" wire:click.stop>
            <div class="bg-white w-full max-w-xl rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold mb-4">Edit Outlet</h2>

                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium">Nama Outlet</label>
                        <input type="text" wire:model="name" class="w-full border p-2 border-neutral-300 rounded-lg"
                            placeholder="Masukkan nama outlet">
                        @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Location</label>
                        <textarea wire:model="location" class="w-full border p-2 border-neutral-300 rounded-lg" rows="3"
                            placeholder="Masukkan lokasi atau alamat lengkap"></textarea>
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

                    <!-- HARI TUTUP SECTION -->
                    <div>
                        <label class="text-sm font-medium block mb-2">Hari Tutup (Opsional)</label>
                        <div class="border border-neutral-300 rounded-lg p-3 space-y-2">
                            <p class="text-xs text-gray-500 mb-2">Pilih hari-hari ketika outlet ini tutup</p>

                            <div class="grid grid-cols-2 gap-2">
                                @foreach($allDays as $day)
                                <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                                    <input type="checkbox" wire:model="closed_days" value="{{ $day->id }}"
                                        class="rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                                    <span class="text-sm">{{ $day->name }}</span>
                                </label>
                                @endforeach
                            </div>

                            @if(!empty($closed_days))
                            <div class="mt-2 pt-2 border-t border-gray-200">
                                <p class="text-xs text-gray-600">
                                    <strong>Dipilih:</strong>
                                    {{ $allDays->whereIn('id', $closed_days)->pluck('name')->join(', ') }}
                                </p>
                            </div>
                            @endif
                        </div>
                        @error('closed_days') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="closeEditModal"
                        class="px-4 py-2 rounded border border-neutral-300 bg-white hover:bg-neutral-200 cursor-pointer">
                        Batal
                    </button>

                    <button wire:click="updateOutlet"
                        class="px-4 py-2 rounded bg-yellow-500 text-white cursor-pointer hover:bg-yellow-600">
                        Update
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- DETAIL OUTLET MODAL (staff list + closed days) -->
    @if($showDetailModal && $detailOutlet)
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 overflow-y-auto no-scrollbar" style="z-index: 999;"
        wire:click="closeDetail">
        <div class="min-h-full flex justify-center items-center py-8 px-4" wire:click.stop>
            <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold mb-4">Detail - {{ $detailOutlet->name }}</h2>

                <div class="space-y-3">
                    <p class="text-sm text-gray-600">Location: {{ $detailOutlet->location }}</p>
                    <p class="text-sm text-gray-600">Status:
                        <span class="px-2 py-1 rounded-full text-xs border
                            {{ $detailOutlet->status === 'AKTIF'
                                ? 'border-blue-700 text-blue-700'
                                : 'border-red-700 text-red-700' }}">
                            {{ $detailOutlet->status }}
                        </span>
                    </p>

                    <!-- HARI TUTUP INFO -->
                    <div class="mt-3 pt-3 border-t">
                        <h3 class="font-medium text-sm mb-2">Hari Tutup</h3>
                        @if($detailOutlet->closedDays->isEmpty())
                        <p class="text-sm text-gray-500">Buka setiap hari</p>
                        @else
                        <div class="flex flex-wrap gap-2">
                            @foreach($detailOutlet->closedDays as $day)
                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs border border-red-300">
                                {{ $day->name }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <div class="mt-3 pt-3 border-t">
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
                    <button wire:click="closeDetail"
                        class="px-4 py-2 rounded border border-neutral-100 bg-white hover:bg-neutral-100 cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>