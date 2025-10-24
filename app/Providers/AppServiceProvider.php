<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\StorageManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
        {
            // Apply active storage configuration automatically on each request
            StorageManager::applyActiveStorage();
        }
}
