<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->integer('cache_seconds')->nullable()->after('type');
        });

        DB::table('settings')->insertOrIgnore([
            'shop_id' => null,
            'group' => 'general',
            'key' => 'Default Setting Cache Time',
            'value' => '300',
            'type' => 'integer',
            'cache_seconds' => 60,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('settings')
            ->where('shop_id', null)
            ->where('group', 'general')
            ->where('key', 'Default Setting Cache Time')
            ->delete();

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('cache_seconds');
        });
    }
};
