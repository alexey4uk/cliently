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
        Schema::table('ticket_categories', function (Blueprint $table) {
            // Удаляем foreign key constraint
            $table->dropForeign(['business_id']);
            // Удаляем колонку
            $table->dropColumn('business_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_categories', function (Blueprint $table) {
            // Восстанавливаем колонку
            $table->foreignId('business_id')->nullable()->after('id');
            // Восстанавливаем foreign key
            $table->foreign('business_id')->references('id')->on('businesses')->cascadeOnDelete();
        });
    }
};
