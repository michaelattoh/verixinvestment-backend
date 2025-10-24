<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->index(); // e.g. general, notifications, currency
            $table->string('key');            // unique within group
            $table->text('value')->nullable(); // store JSON/text
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->string('description')->nullable();
            $table->timestamps();

            $table->unique(['group', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
