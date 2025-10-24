<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestmentsTable extends Migration
{
    public function up()
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // investor
            $table->string('transaction_id')->unique();
            $table->enum('type', ['daily', 'weekly', 'monthly', 'fixed', 'agricultural']);
            $table->decimal('amount', 15, 2);
            $table->decimal('goal_amount', 15, 2)->nullable();
            $table->enum('status', ['pending', 'success', 'cancelled'])->default('pending');
            $table->timestamps();
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('investments');
    }
}
