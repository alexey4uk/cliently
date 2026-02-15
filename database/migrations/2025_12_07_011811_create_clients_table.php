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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('first_name');
            $table->string('last_name')->nullable();

            $table->string('phone', 20)->nullable()->index();
            $table->string('phone_country_code', 2)->nullable();
            $table->string('email')->nullable();

            $table->date('birthday')->nullable();
            $table->text('comment')->nullable();

            $table->string('telegram_user_id')->nullable();
            $table->timestamp('last_reengagement_sent_at')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'last_name', 'first_name'], 'idx_clients_search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
