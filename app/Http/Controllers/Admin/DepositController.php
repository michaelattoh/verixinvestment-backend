<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DepositController extends Controller
{
    // Add Deposit (admin creates deposit for user)
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $txn = Transaction::create([
            'user_id'        => $request->user_id,
            'transaction_id' => strtoupper(Str::random(10)),
            'type'           => 'deposit',
            'amount'         => $request->amount,
            'status'         => 'success', // admin deposits are auto-approved
        ]);

        return response()->json($txn, 201);
    }

    // All Deposits
    public function index(Request $request)
    {
        $query = Transaction::with('user')->where('type', 'deposit');

        // optional search
        if ($request->has('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            })->orWhere('transaction_id', 'like', "%{$request->search}%");
        }

        return response()->json($query->latest()->paginate(10));
    }

    // Deposit Requests (pending deposits)
    public function requests()
    {
        $deposits = Transaction::with('user')
            ->where('type', 'deposit')
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return response()->json($deposits);
    }

    public function approve($id)
{
    $deposit = Transaction::where('type', 'deposit')
        ->where('status', 'pending')
        ->findOrFail($id);

    // update deposit status
    $deposit->update(['status' => 'success']);

    // update wallet balance
    $wallet = $deposit->user->wallet;
    if (!$wallet) {
        $wallet = $deposit->user->wallet()->create(['balance' => 0]);
    }
    $wallet->balance += $deposit->amount;
    $wallet->save();

    return response()->json([
        'message' => 'Deposit approved successfully',
        'deposit' => $deposit
    ]);
}

public function reject($id)
{
    $deposit = Transaction::where('type', 'deposit')
        ->where('status', 'pending')
        ->findOrFail($id);

    $deposit->update(['status' => 'failed']);

    return response()->json([
        'message' => 'Deposit rejected successfully',
        'deposit' => $deposit
    ]);
}

}
