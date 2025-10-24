<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_gateways', 'type')) {
                $table->enum('type', ['online', 'crypto', 'manual', 'custom'])->default('online')->after('name');
            }
            if (!Schema::hasColumn('payment_gateways', 'is_active')) {
                $table->boolean('is_active')->default(false)->after('config');
            }
            if (!Schema::hasColumn('payment_gateways', 'config')) {
                $table->json('config')->nullable()->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->dropColumn(['type', 'is_active']);
        });
    }
};
