<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\InvestmentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Vendor\AuthController as VendorAuthController;
use App\Http\Controllers\User\AuthController as UserAuthController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ReportController;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersReportExport;
use App\Exports\VendorsReportExport;
use App\Exports\InvestmentsReportExport;
use App\Exports\DepositsReportExport;
use App\Exports\WithdrawalsReportExport;
use App\Exports\TransactionsReportExport;
use App\Exports\FinanceReportExport;
use App\Exports\UserLogsReportExport;
use App\Http\Controllers\Admin\SystemSettingsController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\System\InvestmentSettingController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Admin\SystemSettings\PaymentController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\Admin\SocialLoginController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminForgotPasswordController;
use App\Chat\Controllers\GroupController;
use App\Chat\Controllers\MessageController;
use App\Chat\Controllers\MessageAttachmentController;
use App\Chat\Controllers\GroupMemberStatusController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/vendors', VendorController::class);
    Route::apiResource('/investments', InvestmentController::class);
    Route::get('/investments', [InvestmentController::class, 'index']);
    Route::get('/investments/{id}', [InvestmentController::class, 'show']);
    Route::get('/deposits', [DepositController::class, 'index']);        // all deposits
    Route::post('/deposits', [DepositController::class, 'store']);       // add deposit
    Route::get('/deposit-requests', [DepositController::class, 'requests']); // deposit requests
    Route::get('/deposit-requests', [DepositController::class, 'requests']); 
    Route::put('/deposits/{id}/approve', [DepositController::class, 'approve']);
    Route::put('/deposits/{id}/reject', [DepositController::class, 'reject']);
    Route::get('/withdrawals', [WithdrawalController::class, 'index']);         // all withdrawals
    Route::post('/withdrawals', [WithdrawalController::class, 'store']);        // add withdrawal
    Route::get('/withdrawal-requests', [WithdrawalController::class, 'requests']); // requests
    Route::put('/withdrawals/{id}/approve', [WithdrawalController::class, 'approve']);
    Route::put('/withdrawals/{id}/reject', [WithdrawalController::class, 'reject']);
    Route::get('/transactions/history', [TransactionController::class, 'history']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::get('/transactions/export/excel', [TransactionController::class, 'export']);
    Route::get('/transaction-categories', [TransactionCategoryController::class, 'index']);
    Route::post('/transaction-categories', [TransactionCategoryController::class, 'store']);
    Route::get('/transaction-categories/{id}', [TransactionCategoryController::class, 'show']);
    Route::put('/transaction-categories/{id}', [TransactionCategoryController::class, 'update']);
});

//public endpoint for countries (dropdown)
Route::get('countries', function() {
    return response()->json(config('app.countries'));
});

// Vendor mobile auth
Route::prefix('vendor')->group(function () {
    Route::post('/register', [VendorAuthController::class, 'register']);
    Route::post('/login', [VendorAuthController::class, 'login']);
});

// User mobile auth
Route::prefix('user')->group(function () {
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);
});

//Admin - Finance
Route::prefix('admin/finance')->middleware('auth:sanctum')->group(function () {
    Route::get('/users', [FinanceController::class, 'userAccounts'])
        ->middleware('permission:view_dashboard');
    Route::get('/vendors', [FinanceController::class, 'vendorAccounts'])
        ->middleware('permission:view_dashboard');
    Route::get('/summary', [FinanceController::class, 'summary'])
        ->middleware('permission:view_dashboard');
});

// Admin - User management
Route::prefix('admin/user-management')->middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserManagementController::class, 'allUsers'])
        ->middleware('permission:manage_users');
    Route::get('/roles', [UserManagementController::class, 'roles'])
        ->middleware('permission:manage_users');
    Route::get('/permissions', [UserManagementController::class, 'permissions'])
        ->middleware('permission:manage_users');
    Route::post('/roles/assign-permissions', [UserManagementController::class, 'assignPermissions'])
        ->middleware('permission:manage_users');
    Route::post('/users/assign-role', [UserManagementController::class, 'assignRole'])
        ->middleware('permission:manage_users');
});

//reports
Route::prefix('admin/reports')->middleware('auth:sanctum')->group(function () {
    Route::get('/users', [ReportController::class, 'userReports']);
    Route::get('/vendors', [ReportController::class, 'vendorReports']);
    Route::get('/investments', [ReportController::class, 'investmentReports']);
    Route::get('/deposits', [ReportController::class, 'depositReports']);
    Route::get('/withdrawals', [ReportController::class, 'withdrawalReports']);
    Route::get('/transactions', [ReportController::class, 'transactionReports']);
    Route::get('/finance', [ReportController::class, 'financeReports']);
    Route::get('/logs', [ReportController::class, 'userLogReports']);
});

//users report export
Route::get('/admin/reports/users/export', function () {
    return Excel::download(new UsersReportExport, 'users_report.xlsx');
});

//admin reports export
Route::prefix('admin/reports/export')->middleware('auth:sanctum')->group(function () {
    Route::get('/users', fn() => Excel::download(new UsersReportExport, 'users_report.xlsx'));
    Route::get('/vendors', fn() => Excel::download(new VendorsReportExport, 'vendors_report.xlsx'));
    Route::get('/investments', fn() => Excel::download(new InvestmentsReportExport, 'investments_report.xlsx'));
    Route::get('/deposits', fn() => Excel::download(new DepositsReportExport, 'deposits_report.xlsx'));
    Route::get('/withdrawals', fn() => Excel::download(new WithdrawalsReportExport, 'withdrawals_report.xlsx'));
    Route::get('/transactions', fn() => Excel::download(new TransactionsReportExport, 'transactions_report.xlsx'));
    Route::get('/finance', fn() => Excel::download(new FinanceReportExport, 'finance_report.xlsx'));
    Route::get('/logs', fn() => Excel::download(new UserLogsReportExport, 'user_logs_report.xlsx'));
});

//backup
Route::prefix('admin/settings')->middleware(['auth:sanctum','permission:manage_settings'])->group(function () {
    Route::get('/settings', [SystemSettingsController::class, 'index']);
    Route::post('/settings/bulk', [SystemSettingsController::class, 'bulkUpdate']);
    Route::put('/settings/{group}/{key}', [SystemSettingsController::class, 'updateSingle']);
    Route::get('/settings/{group}/{key}', [SystemSettingsController::class, 'show']);

    // backup
    Route::post('/backup/run', [BackupController::class, 'runBackup']);
    Route::get('/backup/list', [BackupController::class, 'listBackups']);
});

//general settings
Route::prefix('admin/settings/general')->middleware('auth:sanctum')->group(function () {
    Route::post('branding', [SystemSettingsController::class, 'uploadBranding']);
    Route::post('site-time', [SystemSettingsController::class, 'updateSettings']); 
    Route::post('access-control', [SystemSettingsController::class, 'updateSettings']); 
});

//reset
Route::post('reset/{group}', [SystemSettingsController::class, 'resetGroup']);

//notifications
Route::prefix('admin/settings/notifications')->middleware('auth:sanctum')->group(function () {
    Route::post('update', [SystemSettingsController::class, 'updateNotificationSettings']);
    Route::post('send-test-email', [SystemSettingsController::class, 'sendTestEmail']);
});

//currency
Route::prefix('admin/settings/currency')->middleware('auth:sanctum')->group(function () {
    Route::post('update', [SystemSettingsController::class, 'updateCurrencySettings']);
    Route::post('preview', [SystemSettingsController::class, 'previewCurrencyFormat']);
    Route::get('admin/settings/currency', [SystemSettingsController::class, 'getCurrencySettings']);
});

//investment setting
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/system-settings/investment', [InvestmentSettingController::class, 'show']);
    Route::put('/system-settings/investment', [InvestmentSettingController::class, 'update']);
});

//aduit log endpoint
Route::middleware(['auth:sanctum', 'role:super-admin'])->group(function () {
    Route::get('/audit-logs', [AuditLogController::class, 'index']);
});


// Group for admin-only routes (you can later protect it with admin auth middleware)
Route::prefix('admin')->group(function () {

    Route::prefix('settings')->group(function () {
        // Deposit Settings
        Route::get('deposit', [PaymentController::class, 'getDepositSettings']);
        Route::post('deposit', [PaymentController::class, 'updateDepositSettings']);

        // Withdrawal Settings
        Route::get('withdrawal', [PaymentController::class, 'getWithdrawalSettings']);
        Route::post('withdrawal', [PaymentController::class, 'updateWithdrawalSettings']);

        // Payment Gateways
        Route::get('payments', [PaymentController::class, 'getPaymentGateways']);
        Route::post('payments/{id}', [PaymentController::class, 'updatePaymentGateway']);
        Route::post('payments/custom', [PaymentController::class, 'addCustomGateway']);
    });
});

// still grouped
Route::prefix('admin/settings/payment')->group(function () {
    Route::get('/', [PaymentGatewayController::class, 'index']);
    Route::post('/create', [PaymentGatewayController::class, 'store']);
    Route::put('/update/{id}', [PaymentGatewayController::class, 'update']);
    Route::patch('/toggle/{id}', [PaymentGatewayController::class, 'toggle']);
});

Route::prefix('system/storage')->group(function () {
    Route::get('/', [App\Http\Controllers\System\StorageSettingsController::class, 'index']);
    Route::post('/{driver}', [App\Http\Controllers\System\StorageSettingsController::class, 'update']);
    Route::get('/test/{driver}', [App\Http\Controllers\System\StorageSettingsController::class, 'testConnection']);
});

//test connection
Route::post('/storage/test-connection', [StorageController::class, 'testConnection']);
Route::post('/storage/save', [StorageController::class, 'save']);

//admin social login
Route::middleware(['auth:api', 'check.permission:manage_social_login'])->group(function () {
    Route::get('/system/social-login', [SocialLoginSettingsController::class, 'index'])->name('social-login.index');
    Route::post('/system/social-login', [SocialLoginSettingsController::class, 'update'])->name('social-login.update');
});

//admin check notification
Route::middleware(['auth:api', 'check.permission:view_notifications'])->group(function () {
    Route::get('/admin/notifications', [NotificationController::class, 'index']);
    Route::post('/admin/notifications/{id}/read', [NotificationController::class, 'markRead']);
});

//admin profile
Route::middleware(['auth:api'])->group(function () {
    Route::get('/admin/profile', [AdminProfileController::class, 'show']);
    Route::put('/admin/profile', [AdminProfileController::class, 'update']);
    Route::put('/admin/password', [AdminProfileController::class, 'changePassword']);
    Route::post('/admin/logout', [AdminProfileController::class, 'logout']);
});

//admin reset password
Route::prefix('admin')->group(function () {
    Route::post('/forgot-password', [AdminForgotPasswordController::class, 'sendResetLink']);
    Route::post('/reset-password', [AdminForgotPasswordController::class, 'reset']);
});


//chat section
// Group routes
Route::middleware(['auth:api'])->group(function () {
    Route::get('/groups', [GroupController::class, 'index']); // List user's groups
    Route::post('/groups', [GroupController::class, 'store']); // Create new group
    Route::get('/groups/{id}', [GroupController::class, 'show']); // View group details
    Route::put('/groups/{id}', [GroupController::class, 'update']); // Update group
    Route::delete('/groups/{id}', [GroupController::class, 'destroy']); // Delete group
    
});

// Message routes
Route::middleware(['auth:api'])->group(function () {
    Route::get('/groups/{groupId}/messages', [MessageController::class, 'index']); // List messages
    Route::post('/messages', [MessageController::class, 'store']); // Send message
    Route::post('/messages/{id}/read', [MessageController::class, 'markRead']); // Mark as read
});

// Message attachment routes
Route::middleware(['auth:api'])->group(function () {
    Route::get('/attachments/{id}/download', [MessageAttachmentController::class, 'download'])
    ->name('attachments.download'); //download
    Route::delete('/attachments/{id}', [MessageAttachmentController::class, 'destroy'])
        ->name('attachments.destroy');
    Route::post('/messages/{message}/attachments', [MessageAttachmentController::class, 'store'])
        ->name('attachments.store'); //upload
});

//group member status
Route::middleware(['auth:api'])->group(function () {
    Route::post('group-member-status/{groupId}/typing', [GroupMemberStatusController::class, 'updateTyping']);
    Route::post('group-member-status/{groupId}/mute', [GroupMemberStatusController::class, 'updateMute']);
});
