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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();

            $table->text('description')->nullable();

            $table->string('timezone')->default('Europe/Minsk');
            $table->string('currency', 3)->default('BYN');

            $table->string('telegram_chat_id')->nullable();
            $table->string('telegram_token', 64)->unique()->nullable();

            $table->boolean('online_booking_enabled')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('is_active');
            $table->index('owner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
