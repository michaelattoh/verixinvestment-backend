<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    // User Accounts
    public function userAccounts()
    {
        $users = User::with('wallet')->get()->map(function ($user) {
            return [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'status' => $user->status,
                'amount' => $user->wallet ? $user->wallet->balance : 0,
                'avatar' => $user->profile_picture,
            ];
        });

        return response()->json($users);
    }

    // Vendor Accounts
    public function vendorAccounts()
    {
        $vendors = Vendor::with('wallet')->get()->map(function ($vendor) {
            return [
                'id'     => $vendor->id,
                'name'   => $vendor->name,
                'email'  => $vendor->email,
                'status' => $vendor->status,
                'amount' => $vendor->wallet ? $vendor->wallet->balance : 0,
                'avatar' => $vendor->logo,
            ];
        });

        return response()->json($vendors);
    }

    //Revenue Dashboard
    public function summary(Request $request)
    {
        $year = $request->query('year', now()->year);

        // Totals
        $totalLiquidity  = Wallet::sum('balance');
        $totalRevenue    = Transaction::where('type', 'deposit')->where('status', 'success')->sum('amount');
        $totalWithdrawals= Transaction::where('type', 'withdrawal')->where('status', 'success')->sum('amount');
        $totalUsers      = User::count();

        // Investment
        $investments = Transaction::selectRaw('type, SUM(amount) as total')
            ->whereIn('type', [
                'daily_savings',
                'weekly_savings',
                'monthly_savings',
                'fixed_investment',
                'agricultural_investment'
            ])
            ->where('status', 'success')
            ->groupBy('type')
            ->pluck('total','type');

            //deposit by month
            $monthlyDeposits = Transaction::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->where('type','deposit')
            ->where('status','success')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->pluck('total','month');

            //recent transactions
            $recentTransactions = Transaction::with('user')
            ->latest()
            ->take(10)
            ->get();

            //recent investments
            $recentInvestments = Transaction::with('user')
            ->whereIn('type', [
                'daily_savings',
                'weekly_savings',
                'monthly_savings',
                'fixed_investment',
                'agricultural_investment'
            ])
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'totals' => [
                'liquidity'   => $totalLiquidity,
                'revenue'     => $totalRevenue,
                'withdrawals' => $totalWithdrawals,
                'users'       => $totalUsers,
            ],
            'charts' => [
                'investments' => $investments,
                'monthly_deposits' => $monthlyDeposits,
            ],
            'recent_transactions' => $recentTransactions,
            'recent_investments'  => $recentInvestments,
        ]);
    }


}
