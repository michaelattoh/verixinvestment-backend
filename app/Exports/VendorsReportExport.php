<?php

namespace App\Exports;

use App\Models\Vendor;
use Maatwebsite\Excel\Concerns\FromCollection;

class VendorsReportExport implements FromCollection
{
    protected $from;
    protected $to;

    public function __construct($from = null, $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        $query = Vendor::with('wallet');

        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [$this->from, $this->to]);
        }

        return $query->get();
    }
}
