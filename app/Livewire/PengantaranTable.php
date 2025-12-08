<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\Delivery;
use Livewire\Attributes\On;
use Livewire\WithPagination;

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


    public function confirmReceived($deliveryId)
    {
        $this->dispatch('confirmReceived', deliveryId: $deliveryId);
    }

    #[On('terima')]
    public function terima($deliveryId)
    {
        $delivery = Delivery::find($deliveryId);

        if (auth()->user()->role->name === 'kurir') {
            session()->flash('error', 'Kurir tidak boleh melakukan konfirmasi.');
            return;
        }

        \DB::table('delivery_confirmations')->insert([
            'id_delivery' => $deliveryId,
            'id_staff' => auth()->id(),
            'received_at' => now(),
        ]);

        $delivery->update([
            'status' => 'SELESAI'
        ]);

        // kirim event ke browser untuk SweetAlert sukses
        $this->dispatch('deliveryReceived');
    }



    public function getDeliveriesProperty()
    {
        $user = auth()->user();

        $query = Delivery::with(['items.item']);

        // 🔥 Filter staff berdasarkan outlet
        if (in_array($user->role_id, [5])) {
            $query->where('id_outlet', $user->outlet_id);
        }

        // 🔥 Filter waktu
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
        }

        // 🔥 Filter kurir (khusus kurir)
        if ($user->role_id == 4) { // Role 4 = kurir
            $query->where('id_kurir', $user->id);
        } elseif (!empty($this->kurir)) {
            $query->where('id_kurir', $this->kurir);
        }

        // 🔥 Filter status
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        // 🔥 Search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->whereHas('kurir', function ($subQ) {
                    $subQ->where('display_name', 'like', '%' . $this->search . '%');
                })->orWhereHas('outlet', function ($subQ) {
                    $subQ->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        // 🔥 Semua hasil tetap paginate 4
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
            'couriers'   => User::where('role_id', 4)->get(),
            'statuses'   => Delivery::select('status')->distinct()->get(),
            'pages'      => $this->pages
        ]);
    }
}
