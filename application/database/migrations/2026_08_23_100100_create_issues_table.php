<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publication_id')->constrained();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('issue_number');
            $table->date('publication_date')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['publication_id', 'issue_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
