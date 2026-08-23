<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insertOrIgnore([
            [
                'shop_id' => null,
                'group' => 'general',
                'key' => 'Default Product Type',
                'value' => '1',
                'type' => 'integer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shop_id' => null,
                'group' => 'general',
                'key' => 'Default Availability Region',
                'value' => '5',
                'type' => 'integer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['global_region_id']);
            $table->dropColumn('global_region_id');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('global_region_id')->nullable()->after('shop_id')->constrained('global_regions');
        });

        DB::table('settings')
            ->where('shop_id', null)
            ->where('group', 'general')
            ->whereIn('key', ['Default Product Type', 'Default Availability Region'])
            ->delete();
    }
};
