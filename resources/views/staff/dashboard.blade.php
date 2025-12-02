@extends('layouts.app')

@section('content')
<div class="p-4">

    <h2 class="text-xl font-bold mb-3">Dashboard Staff Outlet</h2>

    {{-- Notification Box --}}
    <div class="bg-blue-100 p-3 rounded-lg mb-4">
        <strong>Absensi Hari Ini:</strong>
        {{ auth()->user()->isTodayAttendance() ?? 'Belum Absen' }}
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="p-4 bg-white shadow rounded">
            <h4 class="font-bold">Barang Tersedia</h4>
            <p class="text-2xl">{{ $barangTersedia }}</p>
        </div>

        <div class="p-4 bg-white shadow rounded">
            <h4 class="font-bold">Barang Terjual</h4>
            <p class="text-2xl">{{ $barangTerjual }}</p>
        </div>

        <div class="p-4 bg-white shadow rounded">
            <h4 class="font-bold">Barang Rusak</h4>
            <p class="text-2xl">{{ $barangRusak }}</p>
        </div>
    </div>

    {{-- Penerimaan Terbaru --}}
    <h3 class="font-bold mb-3">Penerimaan Terbaru</h3>

    <table class="w-full bg-white shadow rounded">
        <thead>
            <tr class="border-b">
                <th class="p-2">Barang</th>
                <th class="p-2">Jumlah</th>
                <th class="p-2">Tanggal</th>
                <th class="p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penerimaanTerbaru as $p)
                <tr class="border-b">
                    <td class="p-2">{{ $p->returnItem->first()->name ?? '-' }}</td>
                    <td class="p-2">{{ $p->returnItem->count() }}</td>
                    <td class="p-2">{{ $p->returned_at }}</td>
                    <td class="p-2">
                        <a href="{{ route('staff.receiving.form', $p->id) }}" class="text-blue-600">Konfirmasi</a> |
                        <a href="{{ route('staff.error.form', $p->id) }}" class="text-red-600">Laporkan Salah</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
