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
    Schema::create('group_member_status', function (Blueprint $table) {
        $table->id();
        $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        $table->boolean('is_muted')->default(false);
        $table->boolean('is_typing')->default(false);
        $table->timestamps();

        $table->unique(['group_id', 'user_id']); // prevent duplicate entries
    });
}

public function down(): void
{
    Schema::dropIfExists('group_member_status');
}

};
