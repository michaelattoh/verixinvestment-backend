<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event'); // e.g., "updated", "deleted", "created"
            $table->string('model_type'); // e.g., "App\Models\InvestmentSetting"
            $table->unsignedBigInteger('model_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // admin/user/vendor
            $table->string('user_type')->nullable(); // "admin", "user", "vendor"
            $table->json('changes')->nullable(); // store old and new values
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
