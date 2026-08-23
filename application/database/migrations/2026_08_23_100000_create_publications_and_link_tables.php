<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('publication_regions', function (Blueprint $table) {
            $table->foreignId('publication_id')->constrained();
            $table->foreignId('global_region_id')->constrained();
            $table->primary(['publication_id', 'global_region_id']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('publication_id')->nullable()->constrained()->after('shop_id');
        });

        Schema::table('subscription_issues', function (Blueprint $table) {
            $table->foreignId('publication_id')->nullable()->constrained()->after('subscription_id');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_issues', function (Blueprint $table) {
            $table->dropForeign(['publication_id']);
            $table->dropColumn('publication_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['publication_id']);
            $table->dropColumn('publication_id');
        });

        Schema::dropIfExists('publication_regions');
        Schema::dropIfExists('publications');
    }
};
