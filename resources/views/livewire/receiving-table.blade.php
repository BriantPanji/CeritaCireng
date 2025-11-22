<div>
    {{-- Top summary card --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- Pending Deliveries --}}
        <div class="bg-white rounded-xl shadow p-4 flex flex-row gap-4 items-center md:flex-col md">

    {{-- Ikon --}}
    <div class="w-16 h-16 bg-yellow-500 p-3 rounded-full flex-shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
            class="w-10 h-10 text-white">
            <path d="M3 7h10v8H3z" />
            <path d="M13 9h4l3 3v3h-7z" />
            <circle cx="7.5" cy="17.5" r="2" />
            <circle cx="16.5" cy="17.5" r="2" />
        </svg>
    </div>

    {{-- Text --}}
    <div class="flex-1">
        <h3 class="font-semibold text-sm text-gray-800">Pending Deliveries</h3>
        <p class="text-xs text-gray-500">Sedang dikirim</p>

        <div class="mt-2 md:mt-4">
            <p class="text-3xl font-bold text-gray-900">{{ $pendingDeliveries->total() }}</p>
            <p class="text-sm text-gray-500">Total paket</p>
        </div>
    </div>

</div>



        {{-- History --}}
  <div class="bg-white rounded-xl shadow p-4 flex flex-row gap-4 items-center md:flex-col md:items-start">

    <div class="w-16 h-16 bg-yellow-500 p-3 rounded-full flex-shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
            class="w-10 h-10 text-white">
            <circle cx="12" cy="12" r="9" />
            <path d="M12 6v6l4 2" />
        </svg>
    </div>

    <div class="flex-1">
        <h3 class="font-semibold text-sm text-gray-800">History</h3>
        <p class="text-xs text-gray-500">Riwayat penyelesaian</p>

        <div class="mt-2 md:mt-4">
            <p class="text-3xl font-bold text-gray-900">{{ $historyDeliveries->total() }}</p>
            <p class="text-sm text-gray-500">Total selesai</p>
        </div>
    </div>

</div>



        {{-- Total Items --}}
        <div class="bg-white rounded-xl shadow p-4 flex flex-row gap-4 items-center md:flex-col md:items-start">

    <div class="w-16 h-16 bg-yellow-500 p-3 rounded-full flex-shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
            class="w-10 h-10 text-white">
            <path d="M3 7l9-4 9 4-9 4-9-4z" />
            <path d="M3 7v10l9 4 9-4V7" />
            <path d="M12 11v10" />
        </svg>
    </div>

    <div class="flex-1">
        <h3 class="font-semibold text-sm text-gray-800">Total Items</h3>
        <p class="text-xs text-gray-500">
            Filter: {{ ucfirst($filter_range) }}
            @if ($filter_outlet)
                • Outlet #{{ $filter_outlet }}
            @endif
            @if ($filter_status)
                • Status {{ $filter_status }}
            @endif
        </p>

        <div class="mt-2 md:mt-4">
            <p class="text-3xl font-bold text-gray-900">{{ $summary['final'] ?? 0 }}</p>
            <p class="text-sm text-gray-500">items</p>
        </div>
    </div>

</div>



    </div>


    {{-- Filters --}}
    <div class="overflow-x-auto w-full">
        <div class="mt-4 flex flex-col md:flex-row gap-3 items-center">
            <div class="flex gap-2 w-full md:w-auto">
                <select wire:model.live="filter_range" class="bg-white border rounded-xl px-4 py-2 shadow-sm text-sm">
                    <option value="today">Hari Ini</option>
                    <option value="week">1 Minggu</option>
                    <option value="month">1 Bulan</option>
                    <option value="year">1 Tahun</option>
                    <option value="all">Semua</option>
                </select>

                <select wire:model.live="filter_status" class="bg-white border rounded-xl px-4 py-2 shadow-sm text-sm">
                    <option value="">Semua Status</option>
                    <option value="DITUGASKAN">DITUGASKAN</option>
                    <option value="DIKIRIM">DIKIRIM</option> c
                    <option value="SELESAI">SELESAI</option>
                    <option value="DIBATALKAN">DIBATALKAN</option>
                </select>

                <select wire:model.live="filter_outlet" wire:loading.attr="disabled"
                    class="bg-white border rounded-xl px-4 py-2 shadow-sm text-sm">
                    <option value="">Semua Outlet</option>
                    @foreach ($outlets as $o)
                        <option value="{{ $o->id }}">{{ $o->name ?? ($o->display_name ?? "Outlet #{$o->id}") }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Pending Deliveries Table --}}
    <div class="mt-4 bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="p-4 border-b">
            <h4 class="font-semibold">Pending Deliveries (DITUGASKAN / DIKIRIM)</h4>
            <p class="text-sm text-gray-400">Daftar pengiriman yang sedang dalam perjalanan</p>
        </div>

        <div class="overflow-x-auto relative">
            <table class="min-w-max w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-left">Kurir</th>
                        <th class="px-4 py-3 text-left">Outlet</th> <!-- new -->
                        <th class="px-4 py-3 text-left">Jumlah Item</th>
                        <th class="px-4 py-3 text-left">Dikirim (Assigned)</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingDeliveries as $d)
                        <tr class="border-b">
                            <td class="px-4 py-3">#{{ $d->id }}</td>
                            <td class="px-4 py-3">{{ optional($d->kurir)->display_name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ optional($d->outlet)->name ?? ($d->outlet->display_name ?? '-') }}
                            </td> <!-- new -->
                            <td class="px-4 py-3">{{ $d->items->sum('quantity') ?? 0 }} pcs</td>
                            <td class="px-4 py-3">
                                {{ $d->assigned_at ? \Carbon\Carbon::parse($d->assigned_at)->format('d-m-Y H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <button wire:click.live="showDetail({{ $d->id }})"
                                    class="px-3 py-1 rounded 
                        @if ($d->status == 'DITUGASKAN') bg-blue-500 
                        @elseif($d->status == 'DIKIRIM') bg-yellow-500 @endif
                        text-white text-sm">
                                    {{ $d->status == 'DITUGASKAN' ? 'Ditugaskan' : 'Dikirim' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-4 text-gray-500">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

            <div wire:loading class="justify-center items-center p-4 text-gray-500 absolute inset-0 bg-white/70 z-10">
                Memuat data...
            </div>
        </div>

        {{-- CUSTOM PAGINATION --}}
        <div class="overflow-x-auto w-full">
            @if ($pendingDeliveries->hasPages())
                <div class="mt-3 flex items-center justify-center gap-2 py-4">

                    {{-- Previous --}}
                    @if ($pendingDeliveries->onFirstPage())
                        <button class="px-3 py-2 rounded-xl border border-gray-200 bg-gray-100 text-gray-400 text-sm"
                            disabled>
                            &lt;
                        </button>
                    @else
                        <button wire:click="previousPage"
                            class="px-3 py-2 rounded-xl border border-gray-300 bg-white shadow-sm text-sm text-gray-700
                        hover:border-yellow-500 hover:text-yellow-500 transition">
                            &lt;
                        </button>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($pendingDeliveries->getUrlRange(1, $pendingDeliveries->lastPage()) as $page => $url)
                        @if ($page == $pendingDeliveries->currentPage())
                            <button
                                class="px-3 py-2 rounded-xl border border-yellow-500 bg-white shadow-sm text-sm text-yellow-500 font-semibold">
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
                    @if ($pendingDeliveries->hasMorePages())
                        <button wire:click="nextPage"
                            class="px-3 py-2 rounded-xl border border-gray-300 bg-white shadow-sm text-sm text-gray-700
                        hover:border-yellow-500 hover:text-yellow-500 transition">
                            &gt;
                        </button>
                    @else
                        <button class="px-3 py-2 rounded-xl border bg-gray-100 border-gray-200 text-gray-400 text-sm"
                            disabled>
                            &gt;
                        </button>
                    @endif

                </div>
            @endif
        </div>


    </div>

    {{-- History Deliveries Table --}}
    <div class="mt-6 bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="p-4 border-b">
            <h4 class="font-semibold">History (SELESAI / DIBATALKAN)</h4>
            <p class="text-sm text-gray-400">Riwayat penerimaan</p>
        </div>

        <div class="overflow-x-auto relative">
            <table class="min-w-max w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-left">Kurir</th>
                        <th class="px-4 py-3 text-left">Outlet</th> <!-- new -->
                        <th class="px-4 py-3 text-left">Jumlah Item</th>
                        <th class="px-4 py-3 text-left">Diterima</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historyDeliveries as $h)
                        <tr class="border-b">
                            <td class="px-4 py-3">#{{ $h->id }}</td>
                            <td class="px-4 py-3">{{ optional($h->kurir)->display_name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                {{ optional($h->outlet)->name ?? ($h->outlet->display_name ?? '-') }}
                            </td> <!-- new -->
                            <td class="px-4 py-3">{{ $h->items->sum('quantity') ?? 0 }} pcs</td>
                            <td class="px-4 py-3">
                                @php
                                    $dt = $h->delivered_at;
                                @endphp
                                {{ $dt ? \Carbon\Carbon::parse($dt)->format('d-m-Y H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <button wire:click.live="showDetail({{ $h->id }})"
                                    class="px-3 py-1 rounded 
                        @if ($h->status == 'SELESAI') bg-emerald-600 
                        @elseif($h->status == 'DIBATALKAN') bg-red-600 @endif
                        text-white text-sm">
                                    {{ $h->status == 'SELESAI' ? 'Selesai' : 'Dibatalkan' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-4 text-gray-500">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

            <div wire:loading.flex
                class="justify-center items-center p-4 text-gray-500 absolute inset-0 bg-white/70 z-10">
                Memuat data...
            </div>
        </div>

        {{-- CUSTOM PAGINATION --}}
        <div class="overflow-x-auto w-full">
            @if ($historyDeliveries->hasPages())
                <div class="mt-3 flex items-center justify-center gap-2 py-4">

                    {{-- Previous --}}
                    @if ($historyDeliveries->onFirstPage())
                        <button class="px-3 py-2 rounded-xl border border-gray-200 bg-gray-100 text-gray-400 text-sm"
                            disabled>
                            &lt;
                        </button>
                    @else
                        <button wire:click="previousPage"
                            class="px-3 py-2 rounded-xl border border-gray-300 bg-white shadow-sm text-sm text-gray-700
                        hover:border-yellow-500 hover:text-yellow-500 transition">
                            &lt;
                        </button>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($historyDeliveries->getUrlRange(1, $historyDeliveries->lastPage()) as $page => $url)
                        @if ($page == $historyDeliveries->currentPage())
                            <button
                                class="px-3 py-2 rounded-xl border border-yellow-500 bg-white shadow-sm text-sm text-yellow-500 font-semibold">
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
                    @if ($historyDeliveries->hasMorePages())
                        <button wire:click="nextPage"
                            class="px-3 py-2 rounded-xl border border-gray-300 bg-white shadow-sm text-sm text-gray-700
                        hover:border-yellow-500 hover:text-yellow-500 transition">
                            &gt;
                        </button>
                    @else
                        <button class="px-3 py-2 rounded-xl border bg-gray-100 border-gray-200 text-gray-400 text-sm"
                            disabled>
                            &gt;
                        </button>
                    @endif

                </div>
            @endif
        </div>

    </div>

    {{-- Detail Modal --}}
    @if ($showDetail && $selectedDelivery)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 md:p-8">
            <div class="absolute inset-0 bg-black/40" wire:click.live="closeDetail"></div>

            <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl mx-auto overflow-auto flex flex-col"
                style="max-height: 90vh;">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="font-semibold text-lg">Detail Delivery #{{ $selectedDelivery->id }}</h3>
                    <button wire:click.live="closeDetail" class="text-gray-500 hover:text-gray-700">&times;</button>
                </div>

                <div class="p-4 overflow-auto">
                    <p><strong>Status:</strong> {{ $selectedDelivery->status }}</p>
                    <p><strong>Kurir:</strong> {{ optional($selectedDelivery->kurir)->display_name ?? '-' }}</p>
                    <p><strong>Items:</strong></p>
                    <ul>
                        @foreach ($selectedDelivery->items as $item)
                            <li>{{ $item->product_name }} - {{ $item->quantity }} pcs</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
</div>
