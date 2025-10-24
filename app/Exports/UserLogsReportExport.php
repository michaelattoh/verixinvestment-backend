<?php

namespace App\Exports;

use App\Models\UserLog;
use Maatwebsite\Excel\Concerns\FromCollection;

class UserLogsReportExport implements FromCollection
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
        $query = UserLog::with('user');

        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [$this->from, $this->to]);
        }

        return $query->get();
    }
}
