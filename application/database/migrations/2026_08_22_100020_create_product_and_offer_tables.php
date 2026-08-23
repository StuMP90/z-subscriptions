<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_frequencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('days')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('type', 20)->default('product');
            $table->boolean('is_physical')->default(false);
            $table->boolean('is_digital')->default(false);
            $table->boolean('has_variants')->default(false);
            $table->boolean('track_stock')->default(false);
            $table->integer('stock_quantity')->default(0);
            $table->boolean('allow_backorders')->default(false);
            $table->string('download_url')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->unique(['slug']);
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('parent_id')->nullable()->constrained('product_variants');
            $table->unsignedTinyInteger('level')->default(1);
            $table->string('name');
            $table->string('sku')->unique();
            $table->boolean('track_stock')->default(false);
            $table->integer('stock_quantity')->default(0);
            $table->boolean('allow_backorders')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_shop', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained();
            $table->foreignId('shop_id')->constrained();
            $table->primary(['product_id', 'shop_id']);
        });

        Schema::create('product_regions', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained();
            $table->foreignId('global_region_id')->constrained();
            $table->primary(['product_id', 'global_region_id']);
        });

        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants');
            $table->foreignId('shop_id')->nullable()->constrained();
            $table->foreignId('currency_id')->constrained('currencies');
            $table->foreignId('frequency_id')->nullable()->constrained('subscription_frequencies');
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('price', 12, 2)->default(0);
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->boolean('is_setup_offer')->default(false);
            $table->json('setup_config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('offer_regions', function (Blueprint $table) {
            $table->foreignId('offer_id')->constrained();
            $table->foreignId('global_region_id')->constrained();
            $table->primary(['offer_id', 'global_region_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_regions');
        Schema::dropIfExists('offers');
        Schema::dropIfExists('product_regions');
        Schema::dropIfExists('product_shop');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('subscription_frequencies');
    }
};
