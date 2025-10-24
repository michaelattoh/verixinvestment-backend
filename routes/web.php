<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BackupController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin/settings/backup')->group(function () {
    Route::get('/', [BackupController::class, 'index']);
    Route::post('/create', [BackupController::class, 'createBackup']);
    Route::get('/download/{id}', [BackupController::class, 'download']);
    Route::delete('/delete/{id}', [BackupController::class, 'delete']);
});
