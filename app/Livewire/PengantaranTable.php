<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Delivery;
use App\Models\User;
use Livewire\Attributes\On;
use Carbon\Carbon;

class PengantaranTable extends Component
{
    use WithPagination;

    public $search = '';
    public $kurir = '';
    public $status = '';
    public $waktu = 'all';

    protected $queryString = [
        'search' => ['except' => ''],
        'kurir'  => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updating($field)
    {
        if (in_array($field, ['search', 'kurir', 'status', 'waktu'])) {
            $this->resetPage();
        }
    }

    public function confirmBatal($deliveryId)
    {
        $this->dispatch('confirmBatal', deliveryId: $deliveryId);
    }

    #[On('batalkan')]
    public function batal($deliveryId)
    {
        $delivery = Delivery::find($deliveryId);
        if ($delivery) {
            $delivery->status = "DIBATALKAN";
            $delivery->save();
            $this->dispatch('deliveryBatal', deliveryId: $delivery->id);
        }
    }

    public function getDeliveriesProperty()
    {
        $query = Delivery::with(['items.item']);

        switch ($this->waktu) {
            case 'today':
                $query->whereDate('assigned_at', Carbon::today());
                break;

            case 'week':
                $query->where('assigned_at', '>=', Carbon::now()->subWeek()->startOfDay());
                break;

            case 'month':
                $query->where('assigned_at', '>=', Carbon::now()->subMonth()->startOfDay());
                break;

            case 'year':
                $query->where('assigned_at', '>=', Carbon::now()->subYear()->startOfDay());
                break;

            case 'all':
                // Tidak ada filter (Tampilkan semua)
                break;

            default:
                // Default: Tampilkan semua jika filter tidak valid/kosong
                // Ini lebih aman daripada membatasi ke hari ini saja
                break;
        }

        // Filter Kurir
        if (auth()->user()->role->name === 'kurir') {
            // Jika user adalah kurir, paksa filter ke id user tersebut
            $query->where('id_kurir', auth()->id());
        } elseif (!empty($this->kurir)) {
            // Jika bukan kurir (admin/inventaris), gunakan filter dropdown
            $query->where('id_kurir', $this->kurir);
        }

        // Filter Status
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        // Search (Nama Kurir atau Nama Outlet)
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->whereHas('kurir', function ($subQ) {
                    $subQ->where('display_name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('outlet', function ($subQ) {
                    $subQ->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        return $query->orderBy("assigned_at", 'desc')->paginate(4);
    }

    public function getPagesProperty()
    {
        $paginator = $this->deliveries;
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        $show = 3;

        if ($lastPage <= $show) {
            return range(1, $lastPage);
        }

        $start = $currentPage - 1;
        $end = $currentPage + 1;

        if ($start < 1) {
            $start = 1;
            $end = $show; // 3
        }

        if ($end > $lastPage) {
            $end = $lastPage;
            $start = $lastPage - ($show - 1);
        }

        return range($start, $end);
    }

    public function render()
    {
        return view('livewire.pengantaran-table', [
            'deliveries' => $this->deliveries,
            'couriers' => User::where('role_id', 4)->get(),
            'statuses' => Delivery::select('status')->distinct()->get(),
            'pages' => $this->pages
        ]);
    }
}
