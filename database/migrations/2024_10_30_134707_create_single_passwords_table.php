<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('single_passwords', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('password_type')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('hint')->nullable();
            $table->string('notes')->nullable();
            $table->string('website')->nullable();
            $table->tinyInteger('state')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('single_passwords');
    }
};
