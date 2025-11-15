<div class="p-3">

    {{-- FILTER BAR KHUSUS LIVEWIRE --}}
    <div class="mt-4 flex items-center gap-2 overflow-x-auto pb-2">

        {{-- Dropdown Filter Range --}}
        <select wire:model="filter_range"
            class="bg-white border border-gray-200 px-4 py-2 rounded-xl shadow-sm text-sm">

            <option value="today">Hari Ini</option>
            <option value="week">1 Minggu Terakhir</option>
            <option value="month">1 Bulan Terakhir</option>
            <option value="year">1 Tahun Terakhir</option>
        </select>

    </div>

    {{-- TABLE WRAPPER --}}
    <div class="mt-4 bg-white rounded-2xl shadow-md overflow-hidden">

        <div class="overflow-x-auto w-full">
            <table class="min-w-max w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 font-semibold text-left w-12">No</th>
                        <th class="px-4 py-3 font-semibold text-left">Nama</th>
                        <th class="px-4 py-3 font-semibold text-left">Role</th>
                        <th class="px-4 py-3 font-semibold text-left">Tanggal</th>
                        <th class="px-4 py-3 font-semibold text-left">Waktu</th>
                        <th class="px-4 py-3 font-semibold text-left">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($attendances as $attendance)
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-3">{{ $loop->iteration }}</td>

                            <td class="px-4 py-3">
                                {{ $attendance->user->display_name ?? 'Tidak ada nama' }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $attendance->user->role->display_name ?? '-' }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $attendance->attendance_date }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $attendance->attendance_time }}
                            </td>

                            <td class="px-4 py-3">
                                <span
                                    class="px-3 py-1 rounded-lg text-xs 
                            {{ $attendance->status == 'HADIR' ? 'bg-yellow-500 text-white' : 'bg-gray-300 text-gray-700' }}">
                                    {{ $attendance->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-3">
        {{ $attendances->links() }}
    </div>

</div>
