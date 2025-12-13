<div class="p-3">
    {{-- HEADER --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Log Aktivitas</h1>
        <p class="text-sm text-gray-600 mt-1">Pantau semua perubahan data sistem secara real-time</p>
    </div>

    {{-- SEARCH & FILTERS --}}
    <div class="bg-white rounded-2xl shadow-md p-4 mb-4 sticky top-0 z-10">
        {{-- Search Bar --}}
        <div class="mb-4">
            <div class="flex items-center bg-gray-50 px-4 py-3 rounded-xl border border-gray-200 hover:border-primary">
                <i class="ph ph-magnifying-glass text-gray-400"></i>
                <input type="text" wire:model.live="search" 
                    class="ml-2 w-full text-sm focus:outline-none bg-transparent"
                    placeholder="Cari log aktivitas...">
            </div>
        </div>

        {{-- Filters --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            {{-- Log Type Filter --}}
            <select wire:model.live="logType"
                class="bg-white border border-gray-400 px-4 py-2 rounded-xl shadow-sm text-sm cursor-pointer focus:outline-none focus:ring-2 focus:ring-yellow-500">
                <option value="all">Semua Tipe Log</option>
                <option value="inventory">Inventory</option>
                <option value="delivery">Pengantaran</option>
                <option value="item">Item Master</option>
                <option value="daily_report">Laporan Harian</option>
            </select>

            {{-- Date Range Filter --}}
            <select wire:model.live="dateRange"
                class="bg-white border border-gray-400 px-4 py-2 rounded-xl shadow-sm text-sm cursor-pointer focus:outline-none focus:ring-2 focus:ring-yellow-500">
                <option value="today">Hari Ini</option>
                <option value="week">1 Minggu</option>
                <option value="month">1 Bulan</option>
                <option value="year">1 Tahun</option>
                <option value="all">Semua Waktu</option>
            </select>

            {{-- Action Filter (for item & daily report) --}}
            @if(in_array($logType, ['item', 'daily_report']))
                <select wire:model.live="actionFilter"
                    class="bg-white border border-gray-200 px-4 py-2 rounded-xl shadow-sm text-sm cursor-pointer focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <option value="">Semua Aksi</option>
                    @if($logType === 'item')
                        <option value="CREATE">CREATE</option>
                        <option value="UPDATE">UPDATE</option>
                        <option value="DELETE">DELETE</option>
                        <option value="RESTORE">RESTORE</option>
                    @else
                        <option value="VALIDATED">VALIDATED</option>
                        <option value="INVALIDATED">INVALIDATED</option>
                        <option value="UPDATED">UPDATED</option>
                    @endif
                </select>
            @endif
        </div>

        {{-- Stats Summary --}}
        <div class="mt-4 flex items-center justify-between text-sm">
            <span class="text-gray-600">
                Menampilkan <span class="font-semibold text-gray-800">{{ $logs->count() }}</span> 
                dari <span class="font-semibold text-gray-800">{{ $total }}</span> log
            </span>
            <span class="text-gray-500">
                <i class="ph ph-clock text-yellow-500"></i>
                Diperbarui secara otomatis
            </span>
        </div>
    </div>

    {{-- TIMELINE LOGS --}}
    <div class="space-y-4">
        @forelse($logs as $log)
            <div wire:key="log-{{ $log['id'] }}" 
                class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                
                <div class="p-4">
                    <div class="flex items-start gap-4">
                        {{-- Icon --}}
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                                {{ $log['color'] === 'blue' ? 'bg-blue-100 text-blue-600' : '' }}
                                {{ $log['color'] === 'green' ? 'bg-green-100 text-green-600' : '' }}
                                {{ $log['color'] === 'purple' ? 'bg-purple-100 text-purple-600' : '' }}
                                {{ $log['color'] === 'orange' ? 'bg-orange-100 text-orange-600' : '' }}
                                {{ $log['color'] === 'teal' ? 'bg-teal-100 text-teal-600' : '' }}
                                {{ $log['color'] === 'indigo' ? 'bg-indigo-100 text-indigo-600' : '' }}
                                {{ $log['color'] === 'pink' ? 'bg-pink-100 text-pink-600' : '' }}
                                {{ $log['color'] === 'cyan' ? 'bg-cyan-100 text-cyan-600' : '' }}">
                                <i class="{{ $log['icon'] }} text-2xl"></i>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    {{-- Type Badge --}}
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                        {{ $log['color'] === 'blue' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $log['color'] === 'green' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $log['color'] === 'purple' ? 'bg-purple-100 text-purple-700' : '' }}
                                        {{ $log['color'] === 'orange' ? 'bg-orange-100 text-orange-700' : '' }}
                                        {{ $log['color'] === 'teal' ? 'bg-teal-100 text-teal-700' : '' }}
                                        {{ $log['color'] === 'indigo' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                        {{ $log['color'] === 'pink' ? 'bg-pink-100 text-pink-700' : '' }}
                                        {{ $log['color'] === 'cyan' ? 'bg-cyan-100 text-cyan-700' : '' }}">
                                        {{ $log['type_label'] }}
                                    </span>
                                </div>
                                
                                {{-- Timestamp --}}
                                <span class="text-sm text-gray-500 flex items-center gap-1">
                                    <i class="ph ph-clock text-xs"></i>
                                    {{ \Carbon\Carbon::parse($log['timestamp'])->diffForHumans() }}
                                </span>
                            </div>

                            {{-- Action --}}
                            <h3 class="text-sm font-semibold text-gray-800 mb-1">
                                {{ $log['action'] }}
                            </h3>

                            {{-- Description --}}
                            <p class="text-sm text-gray-600">
                                {{ $log['description'] }}
                            </p>

                            {{-- Full Timestamp --}}
                            <p class="text-xs text-gray-400 mt-2">
                                {{ \Carbon\Carbon::parse($log['timestamp'])->format('d F Y, H:i:s') }} WIB
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Timeline Line (except last item) --}}
                @if(!$loop->last)
                    <div class="ml-10 h-6 border-l-2 border-dashed border-gray-200"></div>
                @endif
            </div>
        @empty
            {{-- Empty State --}}
            <div class="bg-white rounded-2xl shadow-md p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ph ph-clipboard-text text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Tidak Ada Log</h3>
                <p class="text-sm text-gray-600">
                    Belum ada aktivitas yang tercatat untuk filter yang dipilih.
                </p>
            </div>
        @endforelse
    </div>

    {{-- CUSTOM PAGINATION --}}
    @if($total > 10)
        <div class="mt-6 flex items-center justify-center gap-2 py-4">
            <div class="flex gap-2">
                {{-- Previous --}}
                @if($this->getPage() == 1)
                    <button class="px-3 py-2 rounded-xl text-gray-300 cursor-not-allowed" disabled>
                        &lt;
                    </button>
                @else
                    <button wire:click="previousPage"
                        class="px-3 py-2 rounded-xl text-gray-700 hover:bg-yellow-50 hover:text-yellow-600 duration-300">
                        &lt;
                    </button>
                @endif

                {{-- Page Numbers --}}
                @foreach($pages as $p)
                    <div wire:key="page-btn-{{ $p }}">
                        @if($p == $this->getPage())
                            <button
                                class="w-11 px-4 py-2 rounded-xl bg-yellow-500 text-white font-semibold">
                                {{ $p }}
                            </button>
                        @else
                            <button wire:click="gotoPage({{ $p }})"
                                class="w-11 px-4 py-2 rounded-xl hover:bg-gray-100 duration-300">
                                {{ $p }}
                            </button>
                        @endif
                    </div>
                @endforeach

                {{-- Next --}}
                @if(ceil($total / 10) > $this->getPage())
                    <button wire:click="nextPage"
                        class="px-3 py-2 rounded-xl text-gray-700 hover:bg-yellow-50 hover:text-yellow-600 duration-300">
                        &gt;
                    </button>
                @else
                    <button class="px-3 py-2 rounded-xl text-gray-300 cursor-not-allowed" disabled>
                        &gt;
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>
