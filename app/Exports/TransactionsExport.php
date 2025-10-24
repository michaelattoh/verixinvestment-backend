<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Get all transactions
     */
    public function collection()
    {
        return Transaction::with('user')->get();
    }

    /**
     * Add headings to the Excel file
     */
    public function headings(): array
    {
        return [
            'Transaction ID',
            'User Name',
            'Email',
            'Type',
            'Amount',
            'Status',
            'Created At',
        ];
    }

    /**
     * Map transaction fields to rows
     */
    public function map($transaction): array
    {
        return [
            $transaction->transaction_id,
            $transaction->user ? $transaction->user->name : 'N/A',
            $transaction->user ? $transaction->user->email : 'N/A',
            ucfirst($transaction->type),
            $transaction->amount,
            ucfirst($transaction->status),
            $transaction->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
