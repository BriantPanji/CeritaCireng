<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $role = '';
    public $selectedUsers = [];
    public $selectAll = false;

    protected $updatesQueryString = ['search', 'role'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRole()
    {
        $this->resetPage();
    }

    public function toggleSelectAll()
    {
        $this->selectAll = ! $this->selectAll;

        if ($this->selectAll) {
            $this->selectedUsers = $this->users->pluck('id')->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function toggleUser($id)
    {
        if (in_array($id, $this->selectedUsers)) {
            $this->selectedUsers = array_values(array_diff($this->selectedUsers, [$id]));
        } else {
            $this->selectedUsers[] = $id;
        }

        $this->selectAll = count($this->selectedUsers) === $this->users->total();
    }

    public function deleteSelected()
    {
        if (empty($this->selectedUsers)) return;

        User::whereIn('id', $this->selectedUsers)->update(['status' => 'NONAKTIF']);

        $this->selectedUsers = [];
        $this->selectAll = false;

        // optional: flash message
        session()->flash('success', 'Pengguna berhasil dinonaktifkan.');
    }

    // Render: hit DB di sini dan kirim $users ke view
    public function render()
    {
        $query = User::query()->where('status', 'AKTIF');

        if ($this->search) {
            $query->where('display_name', 'like', "%{$this->search}%");
        }

        if ($this->role !== '') {
            $query->where('role_id', $this->role);
        }

        $users = $query->paginate(8)->appends([
            'search' => $this->search,
            'role' => $this->role,
        ]);

        return view('livewire.user-management', compact('users'));
    }
}