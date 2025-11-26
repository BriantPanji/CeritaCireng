<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Attendance;
use App\Models\User;

new class extends Component {
    use WithPagination;

    public $refresh = 0;

    

    public function mount()

    {

        $today = now()->toDateString();

        $dayNumber = now()->dayOfWeekIso; // 1=Senin, 7=Minggu

        // Ambil semua user yang aktif

        $users = User::with('outlet')->where('status', 'AKTIF')->get();

        // Buat absensi untuk setiap user jika belum ada

        foreach ($users as $user) {

            // Skip jika user tidak punya outlet

            if (!$user->outlet) {

                continue;

            }
            
            // Skip kalau user punya outlet statusnya nonaktif
            if ($user->outlet->status === 'NONAKTIF'){
                continue;
            }

            // Cek apakah outlet tutup di hari ini

            $isOutletClosed = $user->outlet->isClosedOn($dayNumber);

            // Jika outlet tutup, skip pembuatan absensi

            if ($isOutletClosed) {

                continue;

            }

            // Buat absensi jika outlet buka

            Attendance::firstOrCreate(

                [

                    'id_user' => $user->id,

                    'attendance_date' => $today,

                ],

                [

                    'status' => 'ABSEN',

                ]

            );

        }

    }

    

    public function checkIn($id)
    {
        $attendance = Attendance::where('id', $id)
            ->where('id_user', auth()->id())
            ->firstOrFail();

        if ($attendance->attendance_date !== now()->toDateString()) {
            return;
        }

        $attendance->update([
            'attendance_time' => now()->format('H:i:s'),
            'status' => 'HADIR',
        ]);

        $this->refresh++;
    }

    public function with()
    { 
        return [
            'attendances' => Attendance::with('user')
                ->where('id_user', auth()->id())
                ->orderBy('attendance_date', 'desc')
                ->paginate(8),

            'refresh' => $this->refresh,
        ];
    }
};
?>

<div class="p-3">

    {{-- TABLE --}}
    {{-- TABLE --}}
    <div class="mt-12 bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left whitespace-nowrap">No</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Nama</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Tanggal</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Waktu</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($attendances as $att)
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-3">{{ $loop->iteration }}</td>

                            <td class="px-4 py-3">
                                {{ $att->user->display_name ?? '-' }}
                            </td>

                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($att->attendance_date, 'Asia/Jakarta')->format('d F Y') }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $att->attendance_time ?? '-' }} 
                            </td>

                            <td class="px-4 py-3 rounded-lg">
                                <p x-data
                                    :class="{
                                        'border border-green-500 text-green-500': '{{ $att->status }}'
                                        === 'HADIR',
                                        'border border-primary-200 text-primary-200': '{{ $att->status }}'
                                        === 'IZIN',
                                        'border border-secondary text-secondary': '{{ $att->status }}'
                                        === 'SAKIT',
                                        'border border-neutral-200 text-neutral-200': !['HADIR', 'IZIN', 'SAKIT']
                                            .includes('{{ $att->status }}'),
                                    }"
                                    class="py-1 rounded-xl block text-center text-xs lg:text-1 ">
                                    {{ $att->status }}
                                </p>
                            </td>

                            <td class="px-4 py-3 text-center">

                                @php
                                    $attDate = \Carbon\Carbon::parse($att->attendance_date)->toDateString();
                                    $today = now()->toDateString();
                                    $isToday = $attDate === $today;
                                    $isAbsent = $att->status === 'ABSEN';
                                @endphp

                                @if ($isToday && $isAbsent)
                                    <button wire:click="checkIn({{ $att->id }})"
                                        class="px-4 py-2 rounded-xl bg-green-600 text-white shadow hover:bg-green-700 text-center">
                                        Presensi
                                    </button>
                                @else
                                    <button disabled
                                        class="px-4 py-2 rounded-xl border border-gray-500 bg-gray-200 text-gray-500 shadow cursor-not-allowed">
                                        Presensi
                                    </button>
                                @endif

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-gray-500">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="w-full">
            {{ $attendances->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>