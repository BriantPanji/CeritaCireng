<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Laporan Harian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session()->has('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
            @endif

            @if (session()->has('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                {{ session('error') }}
            </div>
            @endif

            {{-- Info Banner --}}
            <div class="mb-4 bg-primary-50 border-l-4 border-primary p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-primary-200" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-primary-400">
                            <strong>Perhatian:</strong> Mengedit laporan akan membuat versi baru. Laporan lama akan
                            ditandai sebagai "Tidak Valid" dan tetap tersimpan sebagai histori.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <form wire:submit.prevent="updateReport">
                    {{-- Form Header --}}
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Laporan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Outlet</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-100 px-3 py-2 rounded-md">
                                    {{ $report->outlet_name }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Laporan</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-100 px-3 py-2 rounded-md">
                                    {{ $report->report_date->format('d F Y') }}
                                </p>
                            </div>
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Catatan (Opsional)
                                </label>
                                <textarea wire:model="notes" id="notes" rows="1"
                                    class="mt-1 block w-full px-3 py-2 outline-none ring-1 ring-gray-300 hover:ring-primary/70 focus:ring-primary/80 rounded-md shadow-sm text-sm"
                                    placeholder="Tambahkan catatan untuk laporan ini..."></textarea>
                                @error('notes')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Items Table --}}
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Data Barang</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Edit data di bawah sesuai kebutuhan. Kolom "Dikirim" dan "Dikembalikan" tidak bisa diubah
                            karena otomatis dari sistem.
                        </p>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Item
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Stok Awal
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Dikirim (Auto)
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Terjual
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Rusak/Hilang
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Dikembalikan (Auto)
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Stok Tersisa
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($report->items as $item)
                                    <tr wire:key="item-{{ $item->id_item }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $item->item_name }}
                                            <span class="text-gray-500 text-xs block">{{ $item->item_unit }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input wire:model="items.{{ $item->id_item }}.initial_stock" type="number"
                                                min="0"
                                                class="w-24 h-8 px-1 outline-none ring ring-transparent hover:ring-primary/70 focus:ring-primary/80 rounded shadow-sm"
                                                required />
                                            @error("items.{$item->id_item}.initial_stock")
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="bg-gray-100 px-3 py-1 rounded">{{ $item->stock_delivered
                                                }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input wire:model="items.{{ $item->id_item }}.qty_sold" type="number"
                                                min="0"
                                                class="w-24 h-8 px-1 outline-none ring ring-transparent hover:ring-primary/70 focus:ring-primary/80 rounded shadow-sm"
                                                required />
                                            @error("items.{$item->id_item}.qty_sold")
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input wire:model="items.{{ $item->id_item }}.qty_damaged" type="number"
                                                min="0"
                                                class="w-24 h-8 px-1 outline-none ring ring-transparent hover:ring-primary/70 focus:ring-primary/80 rounded shadow-sm"
                                                required />
                                            @error("items.{$item->id_item}.qty_damaged")
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="bg-gray-100 px-3 py-1 rounded">{{ $item->stock_returned
                                                }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input wire:model="items.{{ $item->id_item }}.stock_remained" type="number"
                                                min="0"
                                                class="w-24 h-8 px-1 outline-none ring ring-transparent hover:ring-primary/70 focus:ring-primary/80 rounded shadow-sm"
                                                required />
                                            @error("items.{$item->id_item}.stock_remained")
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="flex justify-end gap-4 mt-6">
                            <a href="{{ route('daily-reports.show', $report->id) }}"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Batal
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-[#eba400] cursor-pointer border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#d69100] active:bg-[#d69100] focus:outline-none focus:border-[#d69100] focus:ring ring-[#d69100] disabled:opacity-25 transition ease-in-out duration-150">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>