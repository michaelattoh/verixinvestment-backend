<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersReportExport implements FromCollection
{
    public function collection()
    {
        return User::with('wallet')->get();
    }
}
