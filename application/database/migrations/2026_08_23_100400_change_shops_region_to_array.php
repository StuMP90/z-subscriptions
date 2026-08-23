<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropForeign(['global_region_id']);
            $table->json('global_region_ids')->nullable();
        });

        foreach (DB::table('shops')->get() as $shop) {
            DB::table('shops')->where('id', $shop->id)->update([
                'global_region_ids' => $shop->global_region_id ? json_encode([$shop->global_region_id]) : null,
            ]);
        }

        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('global_region_id');
        });
    }

    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->foreignId('global_region_id')->nullable()->constrained('global_regions');
            $table->dropColumn('global_region_ids');
        });
    }
};
