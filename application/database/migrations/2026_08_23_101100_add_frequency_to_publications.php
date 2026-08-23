<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->foreignId('publication_frequency_id')->nullable()->constrained()->after('image');
            $table->integer('frequency')->default(1)->after('publication_frequency_id');
        });
    }

    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropForeign(['publication_frequency_id']);
            $table->dropColumn(['publication_frequency_id', 'frequency']);
        });
    }
};
