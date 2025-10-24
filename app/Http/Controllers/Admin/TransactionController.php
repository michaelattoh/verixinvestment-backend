<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;

class TransactionController extends Controller
{
    // Transaction history
    public function history(Request $request)
    {
        $query = Transaction::with('user');

        if ($request->has('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            })->orWhere('transaction_id', 'like', "%{$request->search}%");
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->latest()->paginate(15));
    }

    // View single transaction
    public function show($id)
    {
        $txn = Transaction::with('user')->findOrFail($id);
        return response()->json($txn);
    }

    // Export transactions as Excel
    public function export()
    {
        return Excel::download(new TransactionsExport, 'transactions.xlsx');
    }
}
