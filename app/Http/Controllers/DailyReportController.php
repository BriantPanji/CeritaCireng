<?php

namespace App\Http\Controllers;

use App\Exports\DailyReportExport;
use App\Models\DailyOutletReport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DailyReportController extends Controller
{
    public function show($id)
    {
        $report = DailyOutletReport::with(['outlet.users', 'staff', 'items.item'])
            ->findOrFail($id);

        // Authorization: staff can only view reports from their own outlet
        $user = auth()->user();
        if ($user->role->name === 'staff') {
            if (!$user->outlet_id || $report->id_outlet !== $user->outlet_id) {
                abort(403, 'Anda tidak memiliki akses ke laporan outlet ini.');
            }
        }

        return view('daily-reports.show', compact('report'));
    }

    public function export(Request $request)
    {
        $filters = [
            'outlet' => $request->get('outlet'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'status' => $request->get('status'),
        ];

        $filename = 'laporan-harian-' . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(new DailyReportExport($filters), $filename);
    }
}
