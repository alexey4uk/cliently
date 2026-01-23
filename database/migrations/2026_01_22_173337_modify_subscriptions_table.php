<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Сначала добавляем user_id без foreign key
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
        });

        // Заполняем user_id данными из business_id
        DB::statement('
            UPDATE subscriptions s
            INNER JOIN business_user bu ON s.business_id = bu.business_id AND bu.role = "owner"
            SET s.user_id = bu.user_id
        ');

        Schema::table('subscriptions', function (Blueprint $table) {
            // Удаляем foreign key и unique constraint для business_id
            $table->dropForeign(['business_id']);
            $table->dropUnique(['business_id']);
            $table->dropColumn('business_id');
            
            // Делаем user_id NOT NULL и добавляем foreign key и unique constraint
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Удаляем user_id
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id']);
            $table->dropColumn('user_id');
            
            // Восстанавливаем business_id
            $table->foreignId('business_id')->after('id')->constrained()->cascadeOnDelete();
            $table->unique('business_id');
        });
    }
};
