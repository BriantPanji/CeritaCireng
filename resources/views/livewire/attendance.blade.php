<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Attendance;

new class extends Component {
    use WithPagination;

    public $refresh = 0;

    public function mount()
    {
        $userId = auth()->id();
        $today = now()->toDateString();

        Attendance::firstOrCreate(
            [
                'id_user' => $userId,
                'attendance_date' => $today,
            ],
            [
                'status' => 'ABSEN',
            ]
        );
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

<div>

    {{-- TABLE --}}
    <div class="mt-4 bg-white shadow-md overflow-hidden ">
        <div class="overflow-x-auto w-full">
            <table class="min-w-max w-full text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Waktu</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
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
                                {{ $att->attendance_date }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-lg text-xs 
                                    @if ($att->status === 'HADIR') 
                                        bg-emerald-700 text-white 
                                    @elseif($att->status === 'IZIN')
                                        bg-purple-700 text-white
                                    @elseif($att->status === 'SAKIT')
                                        bg-cyan-600 text-white
                                    @else
                                        bg-red-700 text-white
                                    @endif
                                ">
                                    {{ $att->status }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                {{ $att->attendance_time ?? '-' }}
                            </td>

                            <td class="px-4 py-3">

                                @php
                                    $attDate = \Carbon\Carbon::parse($att->attendance_date)->toDateString();
                                    $today = now()->toDateString();
                                    $isToday = $attDate === $today;
                                    $isAbsent = $att->status === 'ABSEN';
                                @endphp

                                @if ($isToday && $isAbsent)
                                    <button wire:click="checkIn({{ $att->id }})"
                                        class="px-4 py-2 rounded-xl bg-green-600 text-white shadow hover:bg-green-700">
                                        Presensi
                                    </button>
                                @else
                                    <button disabled
                                        class="px-4 py-2 rounded-xl bg-gray-200 text-gray-500 shadow cursor-not-allowed">
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
