<?php

namespace App\Exports;

use App\Models\Wallet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class FinanceReportExport implements FromCollection
{
    public function collection()
    {
        return new Collection([
            [
                'total_balance'  => Wallet::sum('balance'),
                'user_balance'   => Wallet::whereNotNull('user_id')->sum('balance'),
                'vendor_balance' => Wallet::whereNotNull('vendor_id')->sum('balance'),
            ]
        ]);
    }
}
