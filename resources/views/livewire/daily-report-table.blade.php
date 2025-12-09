<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Harian Outlet') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- Filter Section -->
                <div class="p-6 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Laporan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Outlet Filter - Only for Admin/Dev/Inventaris --}}
                        @if(!$isStaff)
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="filterOutlet" class="block text-sm font-medium text-gray-700 mb-2">
                                    Outlet
                                </label>
                            </div>
                            <select wire:model.live="filterOutlet" id="filterOutlet"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                                <option value="">Semua Outlet</option>
                                @foreach ($outlets as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <!-- Status Filter -->
                        <div>
                            <label for="filterStatus" class="block text-sm font-medium text-gray-700 mb-2">
                                Status Validasi
                            </label>
                            <select wire:model.live="filterStatus" id="filterStatus"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="all">Semua</option>
                                <option value="validated">Valid</option>
                                <option value="invalidated">Tidak Valid</option>
                            </select>
                        </div>

                        <!-- Date From -->
                        <div>
                            <label for="filterDateFrom" class="block text-sm font-medium text-gray-700 mb-2">
                                Dari Tanggal
                            </label>
                            <input wire:model.live="filterDateFrom" type="date" id="filterDateFrom"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>

                        <!-- Date To -->
                        <div>
                            <label for="filterDateTo" class="block text-sm font-medium text-gray-700 mb-2">
                                Sampai Tanggal
                            </label>
                            <input wire:model.live="filterDateTo" type="date" id="filterDateTo"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                    </div>

                    <!-- Export Button -->
                    <div class="mt-4">
                        <button wire:click="export"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Export ke Excel
                        </button>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Outlet
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Dibuat Oleh
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($reports as $report)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        #{{ $report->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $report->report_date->format('d M Y') }}
                                        <span class="block text-xs text-gray-500">{{ $report->report_time->format('H:i')
                                            }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $report->outlet_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $report->created_by_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($report->is_validated)
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Valid
                                        </span>
                                        @else
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Tidak Valid
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('daily-reports.show', $report->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            <p class="text-lg font-medium">Tidak ada laporan</p>
                                            <p class="text-sm mt-1">Belum ada laporan harian yang dibuat dengan filter
                                                ini</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $reports->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>