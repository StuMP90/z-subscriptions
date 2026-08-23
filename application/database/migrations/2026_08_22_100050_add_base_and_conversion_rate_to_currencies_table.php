<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->boolean('is_base_currency')->default(false)->after('decimal_places');
            $table->decimal('conversion_rate', 20, 10)->default(1)->after('is_base_currency');
        });
    }

    public function down(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->dropColumn(['is_base_currency', 'conversion_rate']);
        });
    }
};
