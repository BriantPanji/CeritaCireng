<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ReturnModel;
use App\Models\ReturnItem;
use App\Models\ReturnConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    /**
     * Dashboard Staff
     */
    public function dashboard()
    {
        $staff = Auth::user(); // aman, tidak akan error

        // Misal staff punya relasi outlet (optional)
        $outlet = $staff->outlet ?? null;

        // Stat Cards
        $barangTersedia = Item::count();
        $barangTerjual = 0; 
        $barangRusak = 0;

        // Data pengembalian terbaru
        $penerimaanTerbaru = ReturnModel::with(['returnItem', 'staff'])
            ->orderBy('returned_at', 'desc')
            ->limit(5)
            ->get();

        return view('staff.dashboard', compact(
            'staff',
            'outlet',
            'barangTersedia',
            'barangTerjual',
            'barangRusak',
            'penerimaanTerbaru'
        ));
    }

    /**
     * Form Konfirmasi Barang Masuk
     */
    public function receivingForm($id)
    {
        $return = ReturnModel::with('returnItem')->findOrFail($id);

        return view('staff.receiving-form', compact('return'));
    }

    public function submitReceiving(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1024',
        ]);

        ReturnConfirmation::create([
            'id_return'     => $id,
            'id_inventaris' => Auth::id(),
            'notes'         => $request->notes,
            'confirmed_at'  => now(),
        ]);

        return redirect()->route('staff.dashboard')
            ->with('success', 'Penerimaan berhasil dikonfirmasi!');
    }

    /**
     * Form Laporan Kesalahan
     */
    public function errorForm($id)
    {
        $return = ReturnModel::with('returnItem')->findOrFail($id);

        return view('staff.error-form', compact('return'));
    }

    public function submitError(Request $request, $id)
    {
        $request->validate([
            'item_id'        => 'required|exists:items,id',
            'wrong_quantity' => 'required|integer|min:1',
            'reason'         => 'nullable|string|max:1024',
            'photo'          => 'required|image|max:2048',
        ]);

        // Upload foto
        $path = $request->photo->store('return-errors', 'public');

        // Simpan ke tabel return_items
        ReturnItem::create([
            'id_return' => $id,
            'id_item'   => $request->item_id,
            'quantity'  => $request->wrong_quantity,
        ]);

        return redirect()->route('staff.dashboard')
            ->with('success', 'Laporan kesalahan berhasil dikirim!');
    }
}
