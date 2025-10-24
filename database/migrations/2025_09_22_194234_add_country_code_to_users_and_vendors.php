<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('country_code', 5)->nullable()->after('phone');
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->string('country_code', 5)->nullable()->after('phone');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('country_code');
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('country_code');
        });
    }
};

