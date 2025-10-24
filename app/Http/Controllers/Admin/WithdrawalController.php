<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WithdrawalController extends Controller
{
    // Add Withdrawal (admin initiates for a user)
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $user = User::findOrFail($request->user_id);

        // check wallet balance
        if (!$user->wallet || $user->wallet->balance < $request->amount) {
            return response()->json(['message' => 'Insufficient wallet balance'], 400);
        }

        // deduct balance
        $user->wallet->balance -= $request->amount;
        $user->wallet->save();

        // record transaction
        $txn = Transaction::create([
            'user_id'        => $user->id,
            'transaction_id' => strtoupper(Str::random(10)),
            'type'           => 'withdrawal',
            'amount'         => $request->amount,
            'status'         => 'success', // admin withdrawals are auto-approved
        ]);

        return response()->json($txn, 201);
    }

    // All Withdrawals
    public function index(Request $request)
    {
        $query = Transaction::with('user')->where('type', 'withdrawal');

        if ($request->has('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            })->orWhere('transaction_id', 'like', "%{$request->search}%");
        }

        return response()->json($query->latest()->paginate(10));
    }

    // Withdrawal Requests (pending)
    public function requests()
    {
        $withdrawals = Transaction::with('user')
            ->where('type', 'withdrawal')
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return response()->json($withdrawals);
    }

    // Approve Withdrawal
    public function approve($id)
    {
        $withdrawal = Transaction::where('type', 'withdrawal')
            ->where('status', 'pending')
            ->findOrFail($id);

        $user = $withdrawal->user;

        if (!$user->wallet || $user->wallet->balance < $withdrawal->amount) {
            return response()->json(['message' => 'Insufficient wallet balance'], 400);
        }

        // deduct balance
        $user->wallet->balance -= $withdrawal->amount;
        $user->wallet->save();

        $withdrawal->update(['status' => 'success']);

        return response()->json([
            'message' => 'Withdrawal approved successfully',
            'withdrawal' => $withdrawal
        ]);
    }

    // Reject Withdrawal
    public function reject($id)
    {
        $withdrawal = Transaction::where('type', 'withdrawal')
            ->where('status', 'pending')
            ->findOrFail($id);

        $withdrawal->update(['status' => 'failed']);

        return response()->json([
            'message' => 'Withdrawal rejected successfully',
            'withdrawal' => $withdrawal
        ]);
    }
}

