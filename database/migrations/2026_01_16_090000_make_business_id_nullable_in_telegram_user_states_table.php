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
        Schema::table('telegram_user_states', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
            $table->unsignedBigInteger('business_id')->nullable()->change();
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('telegram_user_states', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
            $table->unsignedBigInteger('business_id')->nullable(false)->change();
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });
    }
};
