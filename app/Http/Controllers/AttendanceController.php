<?php

namespace App\Http\Controllers;

use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('user')->get();

        return view('absensi', compact('attendances'));
    }
}
