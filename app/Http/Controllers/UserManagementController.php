<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Asumsi model User

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar
        $query = User::query();

        // Filter search (jika ada)
        if ($request->has('search') && !empty($request->search)) {
            $query->where('display_name', 'like', '%' . $request->search . '%');
        }

        // Filter role (sesuaikan dengan kolom role di DB, misalnya 'role_id')
        if ($request->has('role') && !empty($request->role)) {
            $query->where('role_id', $request->role); // Ganti 'role_id' dengan nama kolom asli
        }

        // Selalu paginate, meski kosong
        $users = User::where('status', 'AKTIF')->paginate(8); 

        return view('userManagement', compact('users')); 
    }

    public function destroy(Request $request)
{
    $ids = json_decode($request->ids, true); 

    if (empty($ids)) {
        return back()->with('error', 'Tidak ada pengguna yang dipilih.');
    }

    User::whereIn('id', $ids)->update([
        'status' => 'NONAKTIF'
    ]);

    return back()->with('success', 'Pengguna berhasil dinonaktifkan.');
}


}