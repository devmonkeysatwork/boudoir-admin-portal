<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DailySummaryExport  implements WithMultipleSheets
{

    use Exportable;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }
    public function sheets(): array
    {
        $sheets = [];

        // Rush Orders Sheet
        if ($this->data['rush_orders']->count() > 0) {
            $sheets[] = new OrdersSheet($this->data['rush_orders'], 'Rush Orders');
        }

        // Production Orders Sheet
        if ($this->data['production_orders']->count() > 0) {
            $sheets[] = new OrdersSheet($this->data['production_orders'], 'Production Orders');
        }

        // Orders on Hold Sheet
        if ($this->data['orders_on_hold']->count() > 0) {
            $sheets[] = new OrdersSheet($this->data['orders_on_hold'], 'Orders on Hold');
        }

        // Orders with Issues Sheet
        if ($this->data['orders_with_issues']->count() > 0) {
            $sheets[] = new OrdersSheet($this->data['orders_with_issues'], 'Orders with Issues');
        }

//        // Summary Sheet
//        $sheets[] = new SummarySheet($this->data);

        return $sheets;
    }
}
