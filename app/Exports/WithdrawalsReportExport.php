<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;

class WithdrawalsReportExport implements FromCollection
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
        $query = Transaction::where('type', 'withdrawal')->with('user');

        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [$this->from, $this->to]);
        }

        return $query->get();
    }
}
