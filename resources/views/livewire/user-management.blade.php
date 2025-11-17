<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\Role;
use App\Models\Outlet;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

new class extends Component {
    use WithPagination;

    // FITUR LIST DATA
    public $search = '';
    public $role = '';
    public $selectedUsers = [];
    public $selectAll = false;

    // MODAL TAMBAH
    public $showModal = false;

    // FORM FIELDS
    public $display_name;
    public $username;
    public $phone;
    public $role_id;
    public $outlet_id;
    public $status = 'AKTIF';
    public $password;

    protected $updatesQueryString = ['search', 'role'];

    // Reset pagination when filtering
    public function updatingSearch() { $this->resetPage(); }
    public function updatingRole() { $this->resetPage(); }

    // ================================
    // Query User
    // ================================
    protected function getUsers()
    {
        $query = User::query(); // semua user, aktif & nonaktif

        if ($this->search) {
            $query->where('display_name', 'like', "%{$this->search}%");
        }

        if ($this->role !== '') {
            $query->where('role_id', $this->role);
        }

        return $query->paginate(8)->withQueryString();
    }


    // ================================
    // SELECT ALL
    // ================================
    public function toggleSelectAll()
    {
        $this->selectAll = ! $this->selectAll;

        if ($this->selectAll) {
            $users = $this->getUsers();
            $this->selectedUsers = $users->pluck('id')->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function toggleUser($id)
    {
        if (in_array($id, $this->selectedUsers)) {
            $this->selectedUsers = array_diff($this->selectedUsers, [$id]);
        } else {
            $this->selectedUsers[] = $id;
        }

        $users = $this->getUsers();
        $this->selectAll = count($this->selectedUsers) === $users->total();
    }

    // ================================
    // DELETE SELECTED
    // ================================
    public function nonActiveSelected()
    {
        if (empty($this->selectedUsers)) return;

        User::whereIn('id', $this->selectedUsers)->update([
            'status' => 'NONAKTIF'
        ]);

        $this->selectedUsers = [];
        $this->selectAll = false;

        session()->flash('success', 'Pengguna berhasil dinonaktifkan.');
    
    }
    public function activeSelected()
    {
        if (empty($this->selectedUsers)) return;

        User::whereIn('id', $this->selectedUsers)->update([
            'status' => 'AKTIF'
        ]);

        $this->selectedUsers = [];
        $this->selectAll = false;

        session()->flash('success', 'Pengguna berhasil diaktifkan.');
    }

    // ================================
    // MODAL TAMBAH
    // ================================
    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        return redirect()->route('users.management')->with('success', 'Pengguna berhasil ditambahkan.');

    }

    public function resetForm()
    {
        $this->display_name = '';
        $this->username = '';
        $this->phone = '';
        $this->role_id = '';
        $this->outlet_id = '';
        $this->status = 'AKTIF';
        $this->password = '';
    }

    public function save()
    {
        $this->validate([
            'display_name' => 'required|string|max:100',
            'username' => 'required|string|max:75|unique:users,username',
            'phone' => 'required|string|max:15',
            'role_id' => 'required|exists:roles,id',
            'outlet_id' => 'required|exists:outlets,id',
            'status' => 'required|in:AKTIF,NONAKTIF',
            'password' => 'required|min:6'
        ]);

        User::create([
            'display_name' => $this->display_name,
            'username'    => $this->username,
            'phone'       => $this->phone,
            'role_id'     => $this->role_id,
            'outlet_id'   => $this->outlet_id,
            'status'      => $this->status,
            'password'    => Hash::make($this->password),
        ]);

        $this->resetForm();
        $this->showModal = false;
        return redirect()->route('users.management')->with('success', 'Pengguna berhasil ditambahkan.');

    }

    // Role untuk Promote/Demote
    public $roleToChange = '';


    public function promoteSelected($roleId)
    {
        if (empty($this->selectedUsers) || !$roleId) return;

        User::whereIn('id', $this->selectedUsers)
            ->update(['role_id' => $roleId]);

        $this->selectedUsers = [];
        $this->selectAll = false;
        return redirect()->route('users.management')->with('success', 'Pengguna berhasil dipromote.');


    }

    public function demoteSelected()
    {
        if (empty($this->selectedUsers) || !$this->roleToChange) return;

        User::whereIn('id', $this->selectedUsers)
            ->update(['role_id' => $this->roleToChange]);

        $this->selectedUsers = [];
        $this->selectAll = false;
        $this->roleToChange = '';

        session()->flash('success', 'Pengguna berhasil didemote.');
    }


    // ================================
    // WITH VARIABLES
    // ================================
    public function with()
    {
        return [
            'users' => $this->getUsers(),
            'roles' => Role::all(),
            'outlets' => Outlet::all(),
        ];
    }
};

?>

<!-- =================================================================== -->
<!--                               BLADE UI                              -->
<!-- =================================================================== -->

<div class="p-4 space-y-4">

    <!-- Search + Role -->
    <div class="flex items-center justify-between">
        <div class="flex items-center bg-white p-2 rounded-lg w-full mr-3 shadow-sm border hover:border-primary cursor-pointer">
            <i class="ph ph-magnifying-glass text-gray-400 text-base"></i>
            <input type="text"
                   wire:model.debounce.300ms="search"
                   placeholder="Cari pengguna"
                   class="ml-2 w-full outline-none text-sm">
        </div>

        <div class="relative" x-data="{ open:false }">
            <button @click="open = !open"
                    class="bg-primary text-white px-4 py-1 rounded-lg shadow text-sm flex items-center gap-1 cursor-pointer">
                Role <i class="ph ph-funnel-simple ml-1"></i>
            </button>

            <div x-show="open" @click.outside="open = false"
                 class="absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-44 text-sm z-50">
                <button wire:click="$set('role','')" class="px-4 py-2 w-full text-left hover:bg-gray-200 border-b cursor-pointer">Semua Role</button>

                @foreach($roles as $r)
                    <button wire:click="$set('role','{{ $r->id }}')" class="px-4 py-2 w-full text-left hover:bg-gray-200 cursor-pointer {{ !$loop->last ? 'border-b' : '' }}">
                        {{ $r->name }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>


    <!-- Header -->
    <div class="flex justify-between items-center flex-wrap sm:flex-nowrap gap-4">
        <h2 class="font-semibold text-base">
            Semua Pengguna <span class="text-gray-400">{{ $users->total() }}</span>
        </h2>

        <button wire:click="openModal"
                class="flex items-center gap-1 bg-white px-3 py-1 rounded-lg shadow text-sm border border-black cursor-pointer">
            Tambah Pengguna
            <i class="ph ph-plus text-sm"></i>
        </button>
    </div>

    <!-- LIST USERS -->
    <div class="bg-white rounded-lg shadow p-3 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <!-- Header -->
            <thead class="bg-gray-50">
                <tr class="text-left text-sm font-semibold text-gray-700">
                    <th class="px-3 py-2">
                        <input type="checkbox" class="cursor-pointer"{{ $selectAll ? 'checked' : '' }}
                            wire:click="toggleSelectAll">
                    </th>
                    <th class="px-3 py-2 cursor-pointer select-none"
                    wire:click="toggleSelectAll">Nama Pengguna</th>
                    <th class="px-3 py-2 text-center">Status</th>
                    <th class="px-3 py-2 text-center">Role</th>
                    <th class="px-3 py-2 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse ($users as $user)
                    <tr class="hover:bg-gray-100 cursor-pointer" wire:key="user-{{ $user->id }}">
                        <!-- Checkbox -->
                        <td class="px-3 py-2">
                            <input type="checkbox" 
                                wire:click.stop="toggleUser({{ $user->id }})"
                                {{ in_array($user->id, $selectedUsers) ? 'checked' : '' }}>
                        </td>

                        <!-- Nama -->
                        <td class="px-3 py-2 cursor-pointer select-none"
                                wire:click="toggleUser({{ $user->id }})">
                                {{ $user->display_name }}
                        </td>

                        <!-- Status -->
                        <td class="px-3 py-2 align-middle text-center">
                            <span class="px-2 py-0.5 text-xs rounded-full bg-transparent border
                                {{ $user->status === 'AKTIF'
                                    ? 'border-blue-700 text-blue-700'
                                    : 'border-red-700 text-red-700' }}"
                                    wire:click="toggleUser({{ $user->id }})">
                                {{ $user->status }}
                            </span>
                        </td>

                        <!-- Role -->
                        <td class="px-3 py-2 align-middle text-center">
                            <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 border border-gray-300 text-gray-700"
                            wire:click="toggleUser({{ $user->id }})">
                                {{ $user->role->name ?? '-' }}
                            </span>
                        </td>

                        <!-- Aksi -->
                        <td class="px-3 py-2 align-middle text-center">
                            <button class="bg-primary text-white px-3 py-1 rounded-lg text-xs shadow"
                                    wire:click.stop>
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-4 text-sm">
                            Tidak ada pengguna ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>



    <div class="flex justify-end gap-2 mt-3">

        <!-- promote demote -->
        <div x-data="{ open:false }" class="relative w-full sm:w-auto">
            <button @click="open = !open"
                class="bg-orange-500 text-white px-4 py-1 rounded-lg shadow text-sm disabled:opacity-50 w-full sm:w-auto cursor-pointer"
                :disabled="!{{ count($selectedUsers) }}">
                Change Role
            </button>

            <!-- Dropdown muncul di atas -->
            <div x-show="open" @click.outside="open = false"
                class="absolute right-0 bottom-full mb-1 bg-white border rounded-lg shadow z-50">
                @foreach($roles->where('id', '!=', 6) as $r)
                    <button wire:click="promoteSelected({{ $r->id }})"
                            class="px-4 py-2 w-full text-left hover:bg-gray-200 cursor-pointer">
                        {{ $r->name }}
                    </button>
                @endforeach
            </div>
        </div>
        <!-- Non Aktif & Aktif -->
        <button wire:click="nonActiveSelected"
            class="bg-red-700 text-white px-4 py-1 rounded-lg shadow text-sm disabled:opacity-50 w-full sm:w-auto cursor-pointer"
            @disabled(empty($selectedUsers))>
            Nonaktif
        </button>
        <button wire:click="activeSelected"
            class="bg-blue-700 text-white px-4 py-1 rounded-lg shadow text-sm disabled:opacity-50 w-full sm:w-auto cursor-pointer"
            @disabled(empty($selectedUsers))>
            Aktif
        </button>
    </div>



    <!-- Pagination -->
    <div class="w-full">
        {{ $users->links('vendor.pagination.custom') }}
    </div>




    @if($showModal)
    <div
        class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 overflow-y-auto no-scrollbar"
        style="z-index: 999;"
        wire:click="closeModal"
    >
        <div
            class="min-h-full flex justify-center items-start py-8 px-4"
            wire:click.stop
        >
            <div
                class="bg-white w-full max-w-md rounded-xl shadow-lg p-6"
            >
                <h2 class="text-lg font-bold mb-4">Tambah Pengguna</h2>

                <div class="space-y-3">

                    <div>
                        <label class="text-sm font-medium">Nama</label>
                        <input type="text" wire:model="display_name"
                            class="w-full border p-2 border-neutral-300 rounded-lg" placeholder="Masukkan nama anda">
                        @error('display_name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Username</label>
                        <input type="text" wire:model="username"
                            class="w-full border p-2 border-neutral-300 rounded-lg" placeholder="Masukkan nama username">
                        @error('username') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Phone</label>
                        <input type="text" wire:model="phone"
                            class="w-full border p-2 border-neutral-300 rounded-lg" placeholder="Masukkan nama nomor telepon">
                        @error('phone') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Role</label>
                        <select wire:model="role_id"
                            class="w-full border p-2 rounded-lg border-neutral-300">
                            <option value="" disabled hidden>Pilih Role</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Outlet</label>
                        <select wire:model="outlet_id"
                            class="w-full border p-2 rounded-lg border-neutral-300">
                            <option value="" disabled hidden selected>Pilih Outlet</option>
                            @foreach($outlets as $o)
                                <option value="{{ $o->id }}">{{ $o->name }}</option>
                            @endforeach
                        </select>
                        @error('outlet_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Status</label>
                        <select wire:model="status"
                            class="w-full border p-2 rounded-lg border-neutral-300">
                            <option value="AKTIF">AKTIF</option>
                            <option value="NONAKTIF">NONAKTIF</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium">Password</label>
                        <input type="password" wire:model="password"
                            class="w-full border p-2 border-neutral-300 rounded-lg" autocomplete="new-password">
                        @error('password') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        wire:click="closeModal"
                        class="px-4 py-2 rounded border border-neutral-300 bg-white hover:bg-neutral-200 cursor-pointer">
                        Batal
                    </button>

                    <button
                        wire:click="save"
                        class="px-4 py-2 rounded bg-primary text-white cursor-pointer">
                        Simpan
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif


</div>
