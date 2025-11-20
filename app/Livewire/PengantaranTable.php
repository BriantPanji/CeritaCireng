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
        $query = Delivery::query();

        switch ($this->waktu) {
            case 'today':
                $query->whereDate('delivered_at', Carbon::today());
                break;

            case 'week':
                $query->where('delivered_at', '>=', Carbon::now()->subWeek()->startOfDay());
                break;

            case 'month':
                $query->where('delivered_at', '>=', Carbon::now()->subMonth()->startOfDay());
                break;

            case 'year':
                $query->where('delivered_at', '>=', Carbon::now()->subYear()->startOfDay());
                break;

            case 'all':
                // Tidak ada filter (Tampilkan semua)
                break;

            default:
                // Fallback: Jika value aneh/kosong, anggap 'today' atau 'all' (pilih salah satu)
                // Disini saya set default ke 'today' agar aman
                $query->whereDate('delivered_at', Carbon::today());
                break;
        }

        // Filter Kurir
        if (!empty($this->kurir)) {
            $query->where('id_kurir', $this->kurir);
        }

        // Filter Status
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        // Search
        if (!empty($this->search)) {
            $query->whereHas('kurir', function ($q) {
                $q->where('display_name', 'like', '%' . $this->search . '%');
            });
        }

        return $query->orderBy("delivered_at", 'desc')->paginate(3);
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
            'couriers' => User::where('role_id', 3)->get(),
            'statuses' => Delivery::select('status')->distinct()->get(),
            'pages' => $this->pages
        ]);
    }
}
