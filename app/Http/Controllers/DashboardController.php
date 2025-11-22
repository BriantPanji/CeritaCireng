<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil outlet dari user yang sedang login
        $user = Auth::user();
        $idOutlet = $user->outlet_id;
        $outlet = Outlet::find($idOutlet);

        // Mengambil data dashboard
        // Outlet
        $totalOutlet = Outlet::count();

        return view('dashboard', [
            'outlet' => $outlet,
            'totalOutlet' => $totalOutlet
        ]);
    }
}
