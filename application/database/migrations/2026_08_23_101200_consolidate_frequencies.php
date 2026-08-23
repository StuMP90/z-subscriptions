<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('publication_frequencies', 'slug')) {
            Schema::table('publication_frequencies', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('name');
                $table->integer('days')->default(30)->after('slug');
            });

            foreach (DB::table('publication_frequencies')->get() as $row) {
                DB::table('publication_frequencies')->where('id', $row->id)->update([
                    'slug' => \Illuminate\Support\Str::slug($row->name),
                    'days' => $row->name === 'week' ? 7 : ($row->name === 'month' ? 30 : 30),
                ]);
            }

            DB::statement('ALTER TABLE publication_frequencies ALTER COLUMN slug SET NOT NULL');

            Schema::table('publication_frequencies', function (Blueprint $table) {
                $table->unique('slug');
            });
        }

        $existingSlugs = DB::table('publication_frequencies')->pluck('slug')->toArray();

        if (Schema::hasTable('subscription_frequencies')) {
            $missing = DB::table('subscription_frequencies')
                ->whereNotIn('slug', $existingSlugs)
                ->get();

            foreach ($missing as $row) {
                DB::table('publication_frequencies')->insert([
                    'name' => $row->name,
                    'slug' => $row->slug,
                    'days' => $row->days,
                    'is_active' => $row->is_active,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::statement('UPDATE offers SET frequency_id = pf.id FROM subscription_frequencies sf, publication_frequencies pf WHERE offers.frequency_id = sf.id AND sf.slug = pf.slug');

            DB::statement('UPDATE subscriptions SET frequency_id = pf.id FROM subscription_frequencies sf, publication_frequencies pf WHERE subscriptions.frequency_id = sf.id AND sf.slug = pf.slug');

            Schema::table('offers', function (Blueprint $table) {
                $table->dropForeign(['frequency_id']);
            });

            Schema::table('offers', function (Blueprint $table) {
                $table->foreign('frequency_id')->references('id')->on('publication_frequencies');
            });

            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropForeign(['frequency_id']);
            });

            Schema::table('subscriptions', function (Blueprint $table) {
                $table->foreign('frequency_id')->references('id')->on('publication_frequencies');
            });

            Schema::dropIfExists('subscription_frequencies');
        }
    }

    public function down(): void
    {
        Schema::create('subscription_frequencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('days')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->dropForeign(['frequency_id']);
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->foreign('frequency_id')->references('id')->on('subscription_frequencies');
        });
    }
};
