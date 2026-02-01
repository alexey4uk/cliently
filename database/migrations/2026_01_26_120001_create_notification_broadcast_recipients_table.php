<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Unique;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notification_broadcast_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_broadcast_id')
                ->constrained(indexName: 'nb_recipients_broadcast_id_foreign')
                ->cascadeOnDelete();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['notification_broadcast_id', 'user_id'], 'nbr_broadcast_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_broadcast_recipients');
    }
};
