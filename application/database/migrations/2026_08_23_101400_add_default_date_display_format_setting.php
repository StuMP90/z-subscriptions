<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insertOrIgnore([
            'shop_id' => null,
            'group' => 'general',
            'key' => 'Default Date Display Format',
            'value' => 'd/m/Y',
            'type' => 'string',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('settings')
            ->where('shop_id', null)
            ->where('group', 'general')
            ->where('key', 'Default Date Display Format')
            ->delete();
    }
};
