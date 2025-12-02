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
        $report = DailyOutletReport::with(['outlet', 'staff', 'items.item'])
            ->findOrFail($id);

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
