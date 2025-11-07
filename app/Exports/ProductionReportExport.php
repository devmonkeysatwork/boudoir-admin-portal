<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductionReportExport implements FromArray, WithHeadings
{
    protected $reportData;

    public function __construct($reportData)
    {
        $this->reportData = $reportData;
    }

    public function array(): array
    {
        return $this->reportData;
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Status',
            'Days in Production',
            'Expected Production Days',
            'Long Production',
            'Deadline',
            'Rush Order'
        ];
    }
}
