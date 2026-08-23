<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
    }

    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
