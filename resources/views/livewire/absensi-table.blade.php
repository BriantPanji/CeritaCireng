<div>

    {{-- Search --}}
    <div class="flex items-center bg-white shadow-reguler px-3 py-3 rounded-xl flex-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.35 4.35a7.5 7.5 0 0012.3 12.3z" />
        </svg>

        <input type="text" wire:model.live="search" placeholder="Cari pengguna"
            class="ml-2 w-full text-sm focus:outline-none">
        <button class="bg-yellow-500 text-white px-4 py-3 rounded-xl shadow">
            Temukan
        </button>
    </div>

    {{-- FILTER --}}
    <div class="flex gap-2 my-4">

        {{-- Filter waktu --}}
        <select wire:model.live="filter_range"
            class="bg-white border border-gray-200 px-4 py-2 rounded-xl shadow-sm text-sm">
            <option value="today">Hari Ini</option>
            <option value="week">1 Minggu</option>
            <option value="month">1 Bulan</option>
            <option value="year">1 Tahun</option>
            <option value="all">Semua</option>
        </select>

        {{-- Filter status --}}
        <select wire:model.live="filter_status"
            class="bg-white border border-gray-200 px-4 py-2 rounded-xl shadow-sm text-sm">
            <option value="">Kehadiran</option>
            <option value="HADIR">Hadir</option>
            <option value="IZIN">Izin</option>
            <option value="SAKIT">Sakit</option>
        </select>

        {{-- Filter role --}}
        <select wire:model.live="filter_role"
            class="bg-white border border-gray-200 px-4 py-2 rounded-xl shadow-sm text-sm">
            <option value="">Role</option>
            <option value="Administrator">Administrator</option>
            <option value="Gudang">Gudang</option>
            <option value="Staff">Staff</option>
            <option value="Pengantar">Pengantar</option>
            <option value="Tamu">Tamu</option>
        </select>
    </div>

    {{-- TABLE --}}
    <div class="mt-4 bg-white rounded-2xl shadow-md overflow-hidden">

        <div class="overflow-x-auto w-full">
            <table class="min-w-max w-full text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Role</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Waktu</th>
                        <th class="px-4 py-3 text-left">Status</th>
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
                                {{ $att->user->role->display_name ?? '-' }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $att->attendance_date }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $att->attendance_time }}
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-gray-500">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CUSTOM PAGINATION --}}
        @if ($attendances->hasPages())
            <div class="mt-3 flex items-center justify-center gap-2 py-4">

                {{-- Previous --}}
                @if ($attendances->onFirstPage())
                    <button class="px-3 py-2 rounded-xl border border-gray-200 bg-gray-100 text-gray-400 text-sm" disabled>
                        &lt;
                    </button>
                @else
                    <button wire:click="previousPage" class="px-3 py-2 rounded-xl border border-gray-300 bg-white shadow-sm text-sm text-gray-700
                        hover:border-yellow-500 hover:text-yellow-500 transition">
                        &lt;
                    </button>
                @endif

                {{-- Page Numbers --}}
                @foreach ($attendances->getUrlRange(1, $attendances->lastPage()) as $page => $url)
                    @if ($page == $attendances->currentPage())
                        <button class="px-3 py-2 rounded-xl border border-yellow-500 bg-white shadow-sm text-sm text-yellow-500 font-semibold">
                            {{ $page }}
                        </button>
                    @else
                        <button wire:click="gotoPage({{ $page }})"
                            class="px-3 py-2 rounded-xl border border-gray-300 bg-white shadow-sm text-sm text-gray-700 hover:border-yellow-500 hover:text-yellow-500 transition">
                            {{ $page }}
                        </button>
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($attendances->hasMorePages())
                    <button wire:click="nextPage" class="px-3 py-2 rounded-xl border border-gray-300 bg-white shadow-sm text-sm text-gray-700
                        hover:border-yellow-500 hover:text-yellow-500 transition">
                        &gt;
                    </button>
                @else
                    <button class="px-3 py-2 rounded-xl border bg-gray-100 border-gray-200 text-gray-400 text-sm" disabled>
                        &gt;
                    </button>
                @endif

            </div>
        @endif

    </div>
</div>

