<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $monthId = DB::table('publication_frequencies')->where('slug', 'month')->value('id')
            ?? DB::table('publication_frequencies')->where('is_active', true)->value('id');

        if ($monthId) {
            DB::table('publications')->whereNull('publication_frequency_id')->update(['publication_frequency_id' => $monthId]);
        }

        DB::table('publications')->whereNull('frequency')->update(['frequency' => 1]);

        DB::statement('ALTER TABLE publications ALTER COLUMN publication_frequency_id SET NOT NULL');
        DB::statement('ALTER TABLE publications ALTER COLUMN frequency SET NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE publications ALTER COLUMN publication_frequency_id DROP NOT NULL');
        DB::statement('ALTER TABLE publications ALTER COLUMN frequency DROP NOT NULL');
    }
};
