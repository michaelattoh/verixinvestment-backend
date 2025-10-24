<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('investment_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('daily_savings_fee', 5, 2)->default(0.00);
            $table->decimal('weekly_savings_fee', 5, 2)->default(0.00);
            $table->decimal('monthly_savings_fee', 5, 2)->default(0.00);
            $table->decimal('fixed_investment_fee', 5, 2)->default(0.00);
            $table->decimal('agricultural_fee', 5, 2)->default(0.00);
            $table->decimal('default_vendor_commission', 5, 2)->default(0.00);
            $table->decimal('max_vendor_commission', 5, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_settings');
    }
};
