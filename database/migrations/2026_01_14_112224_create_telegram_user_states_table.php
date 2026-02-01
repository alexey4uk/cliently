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
        Schema::create('telegram_user_states', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('telegram_user_id'); 
            $table->string('step')->default('start');
            $table->json('data')->nullable();
            $table->unsignedBigInteger('last_message_id')->nullable();
            $table->foreignId('business_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['telegram_user_id', 'business_id'], 'tg_user_business_state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_user_states');
    }
};
