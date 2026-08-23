<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('shop_id')->nullable()->constrained();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants');
            $table->foreignId('offer_id')->constrained();
            $table->foreignId('frequency_id')->constrained('subscription_frequencies');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->date('start_date');
            $table->integer('issues_count')->nullable();
            $table->boolean('is_continuous')->default(false);
            $table->string('status', 20)->default('active');
            $table->timestamp('cancelled_at')->nullable();
            $table->date('next_renewal_date')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('subscription_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained();
            $table->integer('issue_number');
            $table->date('publication_date')->nullable();
            $table->date('mailing_date')->nullable();
            $table->string('mailed_status', 20)->default('pending');
            $table->string('description')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->boolean('is_digital')->default(false);
            $table->string('download_url')->nullable();
            $table->boolean('track_stock')->default(false);
            $table->integer('stock_quantity')->default(0);
            $table->timestamp('mailed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('subscription_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained();
            $table->date('period_start');
            $table->date('period_end');
            $table->integer('issues_count')->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->foreignId('currency_id')->constrained('currencies');
            $table->timestamp('renewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('liabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained();
            $table->foreignId('currency_id')->constrained('currencies');
            $table->integer('total_issues')->default(0);
            $table->integer('issues_mailed')->default(0);
            $table->integer('issues_remaining')->default(0);
            $table->decimal('outstanding_liability', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('liabilities');
        Schema::dropIfExists('subscription_renewals');
        Schema::dropIfExists('subscription_issues');
        Schema::dropIfExists('subscriptions');
    }
};
