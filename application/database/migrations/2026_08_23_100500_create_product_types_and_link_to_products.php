<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('product_types')->insert([
            ['code' => 'standard_product', 'name' => 'Standard Product', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'delivery', 'name' => 'Delivery Charge', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'gift_card', 'name' => 'Gift Card', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'service', 'name' => 'Service', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('product_type_id')->nullable()->after('description')->constrained('product_types');
        });

        DB::statement('
            UPDATE products
            SET product_type_id = (SELECT id FROM product_types WHERE code = ?)
            WHERE product_type_id IS NULL
        ', ['standard_product']);

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('type', 20)->default('product');
            $table->dropForeign(['product_type_id']);
            $table->dropColumn('product_type_id');
        });

        Schema::dropIfExists('product_types');
    }
};
