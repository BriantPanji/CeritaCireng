<div class="p-3">

    {{-- Search --}}
    <div class="mt-4 flex items-center gap-2 ">
        <div class="flex items-center bg-white p-2 rounded-lg w-full mr-3 shadow-sm border hover:border-primary cursor-pointer">
            <i class="ph ph-magnifying-glass"> </i>
            <input type="text" wire:model.live="search" class="ml-2 w-full text-sm focus:outline-none"
                placeholder="Cari absensi">
        </div>
    </div>

    {{-- FILTER --}}
    <div class="mt-4 flex items-center gap-2 overflow-x-auto pb-2">

        {{-- Filter waktu --}}
        <select wire:model.live="filter_range"
            class="bg-white border border-neutral-200 px-3 py-1 rounded-xl shadow-sm text-sm cursor-pointer">
            <option value="today">Hari Ini</option>
            <option value="week">1 Minggu</option>
            <option value="month">1 Bulan</option>
            <option value="year">1 Tahun</option>
            <option value="all">Semua</option>
        </select>

        {{-- Filter status --}}
        <select wire:model.live="filter_status"
            class="bg-white border border-neutral-200 px-3 py-1 rounded-xl shadow-sm text-sm cursor-pointer">
            <option value="">Semua Kehadiran</option>
            <option value="HADIR">Hadir</option>
            <option value="IZIN">Izin</option>
            <option value="SAKIT">Sakit</option>
        </select>

        {{-- Filter role --}}
        <select wire:model.live="filter_role"
            class="bg-white border border-neutral-200 px-3 py-1 rounded-xl shadow-sm text-sm cursor-pointer">
            <option value="">Semua Role</option>
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
                        <th class="px-4 py-3 text-center">Role</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Waktu</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($attendances as $att)
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-3">{{ $loop->iteration }}</td>

                            <td class="px-4 py-3">
                                {{ $att->user->display_name ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-center w-[150px]">
                            <p class="px-2 py-0.5 text-xs rounded-full bg-gray-100 border border-gray-400 text-gray-700">
                                {{ $att->user->role->display_name ?? '-' }}
                            </p>
                            </td>

                            <td class="px-4 py-3">
                            {{ \Carbon\Carbon::parse($att->attendance_date, 'Asia/Jakarta')->format('d F Y') }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $att->attendance_time ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
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
                                    class="p-2 px-3 rounded-xl block text-center text-xs lg:text-1 ">
                                    {{ $att->status }}
                                </p>


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
            <div class="mt-3 flex items-center justify-center gap-2 py-4 flex-col w-full">

                <div class="flex gap-2">
                    {{-- Previous --}}
                    @if ($attendances->onFirstPage())
                        <button class="px-3 py-2 rounded-xl text-neutral-300 text-reguler cursor-not-allowed" disabled>
                            &lt;
                        </button>
                    @else
                        <button wire:click="previousPage"
                            class="px-3 py-2 rounded-xl text-reguler
                        hover:border-primary hover:text-primary duration-300">
                            &lt;
                        </button>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($pages as $p)
                        <div wire:key="page-btn-{{ $p }}">

                            @if ($p == $attendances->currentPage())
                                <button
                                    class="w-11 flex justify-center text-center px-4 py-2 rounded-lg border border-primary text-primary font-semibold">
                                    {{ $p }}
                                </button>
                            @else
                                <button wire:click="gotoPage({{ $p }})"
                                    class="w-11 flex justify-center text-center px-4 py-2 rounded-lg hover:bg-neutral-50 duration-300">
                                    {{ $p }}
                                </button>
                            @endif   

                        </div>
                    @endforeach

                    {{-- Next --}}
                    @if ($attendances->hasMorePages())
                        <button wire:click="nextPage"
                            class="px-3 py-2 rounded-xl text-reguler
                        hover:border-primary hover:text-primary duration-300">
                            &gt;
                        </button>
                    @else
                        <button
                            class="px-3 py-2 rounded-xlborder-gray-200 text-neutral-300 text-reguler cursor-not-allowed"
                            disabled>
                            &gt;
                        </button>
                    @endif
                </div>

                <h1 class="text-neutral-300 text-1 lg:text-reguler">Menampilkan {{ $attendances->count() }} data dari
                    total
                    {{ $attendances->total() }}
                    data.</h1>

            </div>
        @endif
        </div>
    </div>
</div>
