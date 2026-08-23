<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_regions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 3)->unique();
            $table->string('symbol', 10)->nullable();
            $table->integer('decimal_places')->default(2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('global_region_id')->constrained();
            $table->string('name');
            $table->string('iso2', 2)->unique();
            $table->string('iso3', 3)->unique()->nullable();
            $table->string('dial_code', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('counties_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained();
            $table->string('name');
            $table->string('code', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('global_region_id')->nullable()->constrained();
            $table->foreignId('default_currency_id')->constrained('currencies');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->unique();
            $table->string('theme')->default('default');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)->default('staff')->after('name');
            $table->boolean('is_active')->default(true)->after('role');
            $table->foreignId('shop_id')->nullable()->after('is_active')->constrained();
            $table->foreignId('invited_by')->nullable()->after('shop_id')->constrained('users');
            $table->string('invitation_token', 128)->nullable()->unique()->after('invited_by');
            $table->timestamp('invitation_token_expires_at')->nullable()->after('invitation_token');
            $table->timestamp('last_login_at')->nullable()->after('invitation_token_expires_at');
        });

        Schema::create('staff_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('role', 50)->default('staff');
            $table->foreignId('invited_by')->constrained('users');
            $table->string('invitation_token')->unique();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->nullable()->constrained();
            $table->foreignId('global_region_id')->nullable()->constrained();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();

            $table->unique(['shop_id', 'email']);
        });

        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained();
            $table->string('type', 20)->default('shipping');
            $table->boolean('is_default')->default(false);
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('company')->nullable();
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('postcode', 20);
            $table->foreignId('country_id')->constrained();
            $table->foreignId('county_id')->nullable()->constrained('counties_states');
            $table->foreignId('global_region_id')->nullable()->constrained();
            $table->string('phone')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('staff_invitations');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->dropForeign(['invited_by']);
            $table->dropColumn(['role', 'is_active', 'shop_id', 'invited_by', 'invitation_token', 'invitation_token_expires_at', 'last_login_at']);
        });

        Schema::dropIfExists('shops');
        Schema::dropIfExists('counties_states');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('global_regions');
    }
};
