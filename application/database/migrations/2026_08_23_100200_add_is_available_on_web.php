<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_available_on_web')->default(true);
        });

        Schema::table('publications', function (Blueprint $table) {
            $table->boolean('is_available_on_web')->default(true);
        });

        Schema::table('issues', function (Blueprint $table) {
            $table->boolean('is_available_on_web')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropColumn('is_available_on_web');
        });

        Schema::table('publications', function (Blueprint $table) {
            $table->dropColumn('is_available_on_web');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_available_on_web');
        });
    }
};
