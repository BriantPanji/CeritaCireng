<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Delivery;
use App\Models\ReturnItem;
use App\Models\ReturnModel;
use Illuminate\Http\Request;
use App\Models\ReturnConfirmation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
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
