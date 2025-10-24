<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Investment;
use App\Models\Transaction;
use App\Models\Wallet;

class DashboardController extends Controller
{
    public function index()
    {
        // Wallets
        $totalBalance = \App\Models\Wallet::sum('balance');


        // Transactions
    $totalIncome  = \App\Models\Transaction::where('type', 'deposit')->where('status', 'success')->sum('amount');
    $totalExpense = \App\Models\Transaction::where('type', 'withdrawal')->where('status', 'success')->sum('amount');
    $totalSavings = \App\Models\Transaction::whereIn('type', ['daily_savings', 'weekly_savings', 'monthly_savings', 'fixed_investment', 'agricultural_investment'])
                        ->where('status', 'success')
                        ->sum('amount');

        // Expense: sum of withdrawals
        $expense = Transaction::where('type', 'withdrawal')->sum('amount');

        // Total savings: sum of investments
        $totalSavings = Investment::sum('amount');

       // Investment chart breakdown
       $investmentChart = Investment::selectRaw('type, SUM(amount) as total')
       ->groupBy('type')
       ->pluck('total', 'type');

        // Deposits & Withdrawals (for charting & reporting)
    $monthlyDeposits = \App\Models\Transaction::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
    ->where('type', 'deposit')
    ->where('status', 'success')
    ->groupBy('month')
    ->pluck('total', 'month');

    $monthlyWithdrawals = \App\Models\Transaction::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
    ->where('type', 'withdrawal')
    ->where('status', 'success')
    ->groupBy('month')
    ->pluck('total', 'month');

        // Recent transactions
    $recentTransactions = \App\Models\Transaction::with('user')
    ->latest()
    ->take(10)
    ->get();

return response()->json([
    'totals' => [
        'balance' => $totalBalance,
        'income'  => $totalIncome,
        'expense' => $totalExpense,
        'savings' => $totalSavings,
    ],
    'charts' => [
        'monthly_deposits'    => $monthlyDeposits,
        'monthly_withdrawals' => $monthlyWithdrawals,
    ],
    'recent_transactions' => $recentTransactions,
]);

    }
