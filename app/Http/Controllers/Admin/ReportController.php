<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    // Generic date filter
    private function applyFilters($query, $request)
    {
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        } elseif ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('created_at', $request->month)
                  ->whereYear('created_at', $request->year);
        } elseif ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }
        return $query;
    }

    // User Reports
    public function userReports(Request $request)
    {
        $query = User::with('wallet');
        $this->applyFilters($query, $request);
        return response()->json($query->get());
    }

    // Vendor Reports
    public function vendorReports(Request $request)
    {
        $query = Vendor::with('wallet');
        $this->applyFilters($query, $request);
        return response()->json($query->get());
    }

    // Investment Reports
    public function investmentReports(Request $request)
    {
        $query = Transaction::whereIn('type', [
            'daily_savings', 'weekly_savings', 'monthly_savings',
            'fixed_investment', 'agricultural_investment'
        ]);
        $this->applyFilters($query, $request);
        return response()->json($query->with('user')->get());
    }

    // Deposit Reports
    public function depositReports(Request $request)
    {
        $query = Transaction::where('type', 'deposit');
        $this->applyFilters($query, $request);
        return response()->json($query->with('user')->get());
    }

    // Withdrawal Reports
    public function withdrawalReports(Request $request)
    {
        $query = Transaction::where('type', 'withdrawal');
        $this->applyFilters($query, $request);
        return response()->json($query->with('user')->get());
    }

    // Transaction Reports
    public function transactionReports(Request $request)
    {
        $query = Transaction::query();
        $this->applyFilters($query, $request);
        return response()->json($query->with('user')->get());
    }

    // Finance Reports
    public function financeReports(Request $request)
    {
        $totalBalance      = Wallet::sum('balance');
        $userBalance       = Wallet::whereNotNull('user_id')->sum('balance');
        $vendorBalance     = Wallet::whereNotNull('vendor_id')->sum('balance');

        return response()->json([
            'total_balance'  => $totalBalance,
            'user_balance'   => $userBalance,
            'vendor_balance' => $vendorBalance,
        ]);
    }

    // User Log Reports
    public function userLogReports(Request $request)
    {
        $query = UserLog::with('user');
        $this->applyFilters($query, $request);
        return response()->json($query->latest()->get());
    }

    //log activity for reports
    public function exportUserReport()
{
    log_activity('exported', 'User Report Exported', [
        'type' => 'user_report',
        'timestamp' => now()->toDateTimeString()
    ]);

    // Export logic continues...
}
}
