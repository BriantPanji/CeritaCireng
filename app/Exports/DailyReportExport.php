<?php

namespace App\Exports;

use App\Models\DailyOutletReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = DailyOutletReport::with(['outlet', 'staff', 'items']);

        if (!empty($this->filters['outlet'])) {
            $query->where('id_outlet', $this->filters['outlet']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('report_date', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('report_date', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['status'])) {
            if ($this->filters['status'] === 'validated') {
                $query->validated();
            } elseif ($this->filters['status'] === 'invalidated') {
                $query->invalidated();
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Outlet',
            'Staff',
            'Tanggal',
            'Waktu',
            'Status Valid',
            'Total Barang Awal',
            'Total Barang Diantar',
            'Total Barang Terjual',
            'Total Barang Rusak',
            'Total Barang Tersisa',
            'Total Pengeluaran',
            'Notes',
        ];
    }

    public function map($report): array
    {
        return [
            $report->id,
            $report->outlet_name,
            $report->created_by_name,
            $report->report_date->format('Y-m-d'),
            $report->report_time->format('H:i:s'),
            $report->is_validated ? 'Valid' : 'Tidak Valid',
            (int) $report->items->sum('initial_stock'),
            (int) $report->items->sum('stock_delivered'),
            (int) $report->items->sum('qty_sold'),
            (int) $report->items->sum('qty_damaged'),
            (int) $report->items->sum('stock_remained'),
            (int) $report->total_expense,
            $report->notes ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
