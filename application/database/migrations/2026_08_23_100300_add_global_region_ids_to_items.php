<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('global_region_ids')->nullable();
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->json('global_region_ids')->nullable();
        });

        Schema::table('publications', function (Blueprint $table) {
            $table->json('global_region_ids')->nullable();
        });

        Schema::table('issues', function (Blueprint $table) {
            $table->json('global_region_ids')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropColumn('global_region_ids');
        });

        Schema::table('publications', function (Blueprint $table) {
            $table->dropColumn('global_region_ids');
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('global_region_ids');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('global_region_ids');
        });
    }
};
