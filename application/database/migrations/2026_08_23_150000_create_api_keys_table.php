<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(true);
            $table->string('username')->unique();
            $table->string('password');
            $table->string('name')->nullable();
            $table->boolean('is_shop')->default(false);
            $table->boolean('is_partner')->default(false);
            $table->timestamps();
        });

        DB::table('api_keys')->insert([
            [
                'active' => true,
                'username' => 'shop',
                'password' => 'shop',
                'name' => 'Default Shop API',
                'is_shop' => true,
                'is_partner' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'active' => true,
                'username' => 'partner',
                'password' => 'partner',
                'name' => 'Default Partner API',
                'is_shop' => false,
                'is_partner' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
