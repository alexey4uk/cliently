<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Обновить существующие записи, где telegram_token пустой
        DB::table('businesses')
            ->whereNull('telegram_token')
            ->orWhere('telegram_token', '')
            ->update(['telegram_token' => Str::random(32)]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ничего не делаем, так как токены уже сгенерированы
    }
};
