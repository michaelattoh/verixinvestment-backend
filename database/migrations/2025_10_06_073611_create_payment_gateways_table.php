<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Gateway name (e.g., PayPal, Stripe)
            $table->enum('type', ['online', 'crypto', 'manual', 'custom'])->default('online'); // Type of gateway
            $table->json('config')->nullable(); // JSON config (keys, secrets, etc.)
            $table->boolean('is_active')->default(false); // Enable/disable
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
