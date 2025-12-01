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

    // MODAL EDIT
    public $showEditModal = false;
    public $editUserId;


    // MODAL DETAIL
    public $showDetailModal = false;
    public $detailUser;

    // FORM FIELDS
    public $display_name;
    public $username;
    public $phone;
    public $role_id;
    public $outlet_id;
    public $status = 'AKTIF';
    public $password;
    public $password_confirmation; // Untuk modal tambah
    public $old_password; // Untuk modal edit
    public $new_password; // Untuk modal edit

    // OLD VALUES untuk placeholder di edit modal
    public $old_display_name;
    public $old_username;
    public $old_phone;

    protected $updatesQueryString = ['role' => ['except' => ''],];

    // Reset pagination when filtering - LIVE SEARCH
    public function updatedSearch() { 
        $this->resetPage();
        $this->selectAll = false;
        $this->selectedUsers = [];
    }
    
    public function updatedRole() { 
        $this->resetPage();
        $this->selectAll = false;
        $this->selectedUsers = [];
    }

    // ================================
    // Query User - INDEPENDEN
    // ================================
    protected function getUsers()
    {
        $query = User::query();

        // Search dan Role filter bekerja bersamaan (independen)
        if ($this->search !== '') {
            $query->where('display_name', 'like', "%{$this->search}%");
        }

        if ($this->role !== '') {
            $query->where('role_id', $this->role);
        }

        return $query->paginate(5)->withQueryString();
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
        $this->showEditModal = false;
        session()->flash('success', 'Tidak jadi adanya perubahan');

    }

    public function openEditModal($id)
    {
        $user = User::findOrFail($id);

        $this->editUserId  = $id;
        $this->display_name = $user->display_name;
        $this->username     = $user->username;
        $this->phone        = $user->phone;
        $this->role_id      = $user->role_id;
        $this->outlet_id    = $user->outlet_id;
        $this->status       = $user->status;

        // Simpan old values untuk placeholder
        $this->old_display_name = $user->display_name;
        $this->old_username     = $user->username;
        $this->old_phone        = $user->phone;

        // Reset password fields
        $this->old_password = '';
        $this->new_password = '';

        $this->showEditModal = true;
    }

    public function update()
    {
        $rules = [
            'display_name' => 'required|string|max:100',
            'phone'        => 'required|string|max:15',
            'role_id'      => 'required|exists:roles,id',
            'outlet_id'    => 'required|exists:outlets,id',
            'status'       => 'required|in:AKTIF,NONAKTIF',
        ];

        // Validasi password hanya jika salah satu field password diisi
        if ($this->old_password || $this->new_password) {
            $rules['old_password'] = 'required';
            $rules['new_password'] = 'required|min:6';
        }

        $this->validate($rules);

        $user = User::findOrFail($this->editUserId);

        // Cek password lama jika mau ganti password
        if ($this->old_password && $this->new_password) {
            if (!Hash::check($this->old_password, $user->password)) {
                $this->addError('old_password', 'Password lama tidak sesuai.');
                return;
            }

            $user->update([
                'display_name' => $this->display_name,
                'phone'        => $this->phone,
                'role_id'      => $this->role_id,
                'outlet_id'    => $this->outlet_id,
                'status'       => $this->status,
                'password'     => Hash::make($this->new_password)
            ]);
        } else {
            // Update tanpa password
            $user->update([
                'display_name' => $this->display_name,
                'phone'        => $this->phone,
                'role_id'      => $this->role_id,
                'outlet_id'    => $this->outlet_id,
                'status'       => $this->status,
            ]);
        }

        $this->showEditModal = false;

        session()->flash('success', 'Data pengguna berhasil diupdate.');
    }



    public function openDetailModal($id)
    {
        return redirect()->route('user.details', $id);
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
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
        $this->password_confirmation = '';
        $this->old_password = '';
        $this->new_password = '';
        $this->old_display_name = '';
        $this->old_username = '';
        $this->old_phone = '';
    }

    public function save()
    {
        $this->validate([
            'display_name' => 'required|string|max:100',
            'username' => 'required|string|max:75|unique:users,username',
            'phone' => 'required|string|max:15',
            'role_id' => 'required|exists:roles,id',
            'outlet_id' => 'required|exists:outlets,id',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
        ], [
            'password_confirmation.same' => 'Konfirmasi password tidak cocok dengan password.'
        ]);

        User::create([
            'display_name' => $this->display_name,
            'username'    => $this->username,
            'phone'       => $this->phone,
            'role_id'     => $this->role_id,
            'outlet_id'   => $this->outlet_id,
            'status'      => 'AKTIF', // Default AKTIF
            'password'    => Hash::make($this->password),
        ]);

        $this->resetForm();
        $this->showModal = false;

        session()->flash('success', 'User berhasil ditambahkan.');
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

        session()->flash('success', 'User berhasil dipromote.');

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

    public function deleteUser()
    {
        if (empty($this->selectedUsers)) return;

        User::whereIn('id', $this->selectedUsers)->delete();

        $this->selectedUsers = [];
        $this->selectAll = false;

        session()->flash('success', 'User berhasil dihapus.');
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

    public function setRole($id)
    {
        $this->role = $id;
        // TIDAK RESET SEARCH lagi, biarkan independen
        $this->resetPage();
    }

};

?>

<div class="px-3 pt-3 space-y-4">
    <!-- Search + Role -->
    <div class="flex items-center justify-between">
        <div class="flex items-center bg-white p-2 rounded-lg w-full mr-3 shadow-sm border hover:border-primary">
            <i class="ph ph-magnifying-glass text-gray-400 text-base"></i>
            <input type="text" wire:model.live.debounce.150ms="search" placeholder="Cari pengguna"
                class="ml-2 w-full outline-none text-sm" autocomplete="off">
        </div>

        <div class="relative" x-data="{ open:false }">
            <button @click="open = !open"
                class="bg-primary text-white px-4 py-1 rounded-lg shadow text-sm flex items-center gap-1 cursor-pointer">
                Role <i class="ph ph-funnel-simple ml-1"></i>
            </button>

            <div x-show="open" @click.outside="open = false"
                class="absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-44 text-sm z-50">
                <button wire:click="setRole('')"
                    class="px-4 py-2 w-full text-left hover:bg-gray-200 border-b cursor-pointer">Semua Role</button>

                @foreach($roles as $r)
                <button wire:click="setRole({{ $r->id }})"
                    class="px-4 py-2 w-full text-left hover:bg-gray-200 cursor-pointer {{ !$loop->last ? 'border-b' : '' }}">
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
                        <input type="checkbox" class="cursor-pointer" {{ $selectAll ? 'checked' : '' }}
                            wire:click="toggleSelectAll">
                    </th>
                    <th class="px-3 py-2 cursor-pointer select-none" wire:click="toggleSelectAll">Nama Pengguna</th>
                    <th class="px-3 py-2 text-center">Status</th>
                    <th class="px-3 py-2 text-center">Role</th>
                    <th class="px-3 py-2 text-center">Edit</th>
                    <th class="px-3 py-2 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse ($users as $user)
                <tr class="hover:bg-gray-100 cursor-pointer" wire:key="user-{{ $user->id }}">
                    <!-- Checkbox -->
                    <td class="px-3 py-2">
                        <input type="checkbox" wire:click.stop="toggleUser({{ $user->id }})" {{ in_array($user->id,
                        $selectedUsers) ? 'checked' : '' }}>
                    </td>

                    <!-- Nama -->
                    <td class="py-2 px-3 cursor-pointer select-none text-1" wire:click="toggleUser({{ $user->id }})">
                        {{ $user->display_name }}
                    </td>

                    <!-- Status -->
                    <td class="px-3 py-2 align-middle text-center w-[100px]">
                        <p class="px-1 py-0.5 text-xs rounded-full bg-gray-100 border
                                {{ $user->status === 'AKTIF'
                                    ? 'border-blue-700 text-blue-700'
                                    : 'border-red-700 text-red-700' }}" wire:click="toggleUser({{ $user->id }})">
                            {{ $user->status }}
                        </p>
                    </td>

                    <!-- Role -->
                    <td class="px-3 py-2 align-middle text-center w-[130px]">
                        <p class="px-2 py-0.5 text-xs rounded-full bg-gray-100 border border-gray-400 text-gray-700"
                            wire:click="toggleUser({{ $user->id }})">
                            {{ $user->role->display_name ?? '-' }}
                        </p>
                    </td>

                    <!-- Edit -->
                    <td class="px-3 py-2 align-middle text-center cursor-pointer w-[100px]">
                        <button class="bg-green-600 text-white px-5 py-1 rounded-lg text-xs shadow cursor-pointer"
                            wire:click.stop="openEditModal({{ $user->id }})">
                            Edit
                        </button>
                    </td>

                    <!-- Aksi -->
                    <td class="px-3 py-2 align-middle text-center w-[100px]">
                        <button class="bg-primary text-white px-3 py-1 rounded-lg text-xs shadow cursor-pointer"
                            wire:click.stop="openDetailModal({{ $user->id }})">
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
                Role
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
        <button wire:click.stop="deleteUser"
            class="bg-neutral-200 text-white px-4 py-1 rounded-lg shadow text-sm disabled:opacity-50 w-full sm:w-auto cursor-pointer"
            @disabled(empty($selectedUsers))>
            Hapus
        </button>
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




    <!-- MODAL TAMBAH (Status dihapus, Password + Confirm Password) -->
    @if($showModal)
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 overflow-y-auto no-scrollbar" style="z-index: 999;"
        wire:click="closeModal">
        <div class="min-h-full flex justify-center items-center py-8 px-4" wire:click.stop>
            <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6">
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
                        <input type="text" wire:model="username" class="w-full border p-2 border-neutral-300 rounded-lg"
                            placeholder="Masukkan username">
                        @error('username') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Phone</label>
                        <input type="text" wire:model="phone" class="w-full border p-2 border-neutral-300 rounded-lg"
                            placeholder="Masukkan nomor telepon">
                        @error('phone') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Role</label>
                        <select wire:model="role_id" class="w-full border p-2 rounded-lg border-neutral-300">
                            <option value="" disabled hidden>Pilih Role</option>
                            @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Outlet</label>
                        <select wire:model="outlet_id" class="w-full border p-2 rounded-lg border-neutral-300">
                            <option value="" disabled hidden selected>Pilih Outlet</option>
                            @foreach($outlets as $o)
                            <option value="{{ $o->id }}">{{ $o->name }}</option>
                            @endforeach
                        </select>
                        @error('outlet_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Password</label>
                        <input type="password" wire:model="password"
                            class="w-full border p-2 border-neutral-300 rounded-lg" placeholder="Masukkan password"
                            autocomplete="new-password">
                        @error('password') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Konfirmasi Password</label>
                        <input type="password" wire:model="password_confirmation"
                            class="w-full border p-2 border-neutral-300 rounded-lg"
                            placeholder="Masukkan ulang password" autocomplete="new-password">
                        @error('password_confirmation') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="closeModal"
                        class="px-4 py-2 rounded border border-neutral-300 bg-white hover:bg-neutral-200 cursor-pointer">
                        Batal
                    </button>

                    <button wire:click="save" class="px-4 py-2 rounded bg-primary text-white cursor-pointer">
                        Simpan
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif

    @if($showDetailModal)
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 overflow-y-auto" wire:click="closeDetailModal">
        <div class="min-h-full flex justify-center items-center py-8 px-4" wire:click.stop>
            <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6">

                <h2 class="text-lg font-bold mb-4">Detail Pengguna</h2>

                @if($detailUser)
                <div class="space-y-3 text-sm">

                    <div>
                        <p class="font-medium">Nama</p>
                        <p class="text-gray-700">{{ $detailUser->display_name }}</p>
                    </div>

                    <div>
                        <p class="font-medium">Username</p>
                        <p class="text-gray-700">{{ $detailUser->username }}</p>
                    </div>

                    <div>
                        <p class="font-medium">Phone</p>
                        <p class="text-gray-700">{{ $detailUser->phone }}</p>
                    </div>

                    <div>
                        <p class="font-medium">Role</p>
                        <p class="text-gray-700">{{ $detailUser->role->display_name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="font-medium">Outlet</p>
                        <p class="text-gray-700">{{ $detailUser->outlet->name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="font-medium">Status</p>
                        <span class="px-2 py-1 rounded-full text-xs border
                            {{ $detailUser->status === 'AKTIF'
                                ? 'border-blue-700 text-blue-700'
                                : 'border-red-700 text-red-700' }}">
                            {{ $detailUser->status }}
                        </span>
                    </div>

                    <div>
                        <p class="font-medium">Dibuat pada</p>
                        <p class="text-gray-700">
                            {{ $detailUser->created_at->format('d F Y, H:i') }}
                        </p>
                    </div>

                    <div>
                        <p class="font-medium">Terakhir diupdate</p>
                        <p class="text-gray-700">
                            {{ $detailUser->updated_at->format('d F Y, H:i') }}
                        </p>
                    </div>

                </div>
                @endif

                <div class="mt-6 flex justify-end">
                    <button wire:click="closeDetailModal"
                        class="px-4 py-2 rounded bg-primary text-white cursor-pointer">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif

    <!-- MODAL EDIT (Ada Status + Password Lama & Baru Opsional) -->
    @if($showEditModal)
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 overflow-y-auto no-scrollbar" style="z-index: 999;"
        wire:click="showEditModal = false">
        <div class="min-h-full flex justify-center items-center py-8 px-4" wire:click.stop>
            <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold mb-4">Edit Pengguna</h2>

                <div class="space-y-3">

                    <div>
                        <label class="text-sm font-medium">Nama</label>
                        <input type="text" wire:model="display_name"
                            class="w-full border p-2 border-neutral-300 rounded-lg" placeholder="{{$old_display_name}}">
                        @error('display_name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Username</label>
                        <input type="text" wire:model="username"
                            class="w-full border p-2 border-neutral-300 rounded-lg bg-gray-100"
                            placeholder="{{$old_username}}" disabled readonly>
                        <p class="text-xs text-gray-500 mt-1">Username tidak dapat diubah</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium">Phone</label>
                        <input type="text" wire:model="phone" class="w-full border p-2 border-neutral-300 rounded-lg"
                            placeholder="{{$old_phone}}">
                        @error('phone') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Role</label>
                        <select wire:model="role_id" class="w-full border p-2 rounded-lg border-neutral-300">
                            <option value="" disabled hidden>Pilih Role</option>
                            @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Outlet</label>
                        <select wire:model="outlet_id" class="w-full border p-2 rounded-lg border-neutral-300">
                            <option value="" disabled hidden>Pilih Outlet</option>
                            @foreach($outlets as $o)
                            <option value="{{ $o->id }}">{{ $o->name }}</option>
                            @endforeach
                        </select>
                        @error('outlet_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Status</label>
                        <select wire:model="status" class="w-full border p-2 rounded-lg border-neutral-300">
                            <option value="AKTIF">AKTIF</option>
                            <option value="NONAKTIF">NONAKTIF</option>
                        </select>
                    </div>

                    <!-- DIVIDER -->
                    <div class="border-t pt-3 mt-3">
                        <p class="text-sm font-medium text-gray-700 mb-2">Ganti Password (Opsional)</p>
                        <p class="text-xs text-gray-500 mb-3">Isi kedua field di bawah hanya jika ingin mengganti
                            password</p>

                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium">Password Lama</label>
                                <input type="password" wire:model="old_password"
                                    class="w-full border p-2 border-neutral-300 rounded-lg"
                                    placeholder="Masukkan password lama" autocomplete="current-password">
                                @error('old_password') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-sm font-medium">Password Baru</label>
                                <input type="password" wire:model="new_password"
                                    class="w-full border p-2 border-neutral-300 rounded-lg"
                                    placeholder="Masukkan password baru (min. 6 karakter)" autocomplete="new-password">
                                @error('new_password') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="closeModal"
                        class="px-4 py-2 rounded border border-neutral-300 bg-white hover:bg-neutral-200 cursor-pointer">
                        Batal
                    </button>

                    <button wire:click="update" class="px-4 py-2 rounded bg-primary text-white cursor-pointer">
                        Simpan
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif
</div>