<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('storage_settings', function (Blueprint $table) {
            $table->id();
            $table->string('driver'); // e.g., local, s3, digitalocean, wasabi, backblaze, gcs
            $table->json('config')->nullable(); // JSON object of key/values per driver
            $table->boolean('is_active')->default(false); // only one can be active at a time
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_settings');
    }
};
