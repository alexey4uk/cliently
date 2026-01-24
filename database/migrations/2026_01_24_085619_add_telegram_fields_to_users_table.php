<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_chat_id')->nullable()->after('avatar');
            $table->string('telegram_token', 64)->unique()->nullable()->after('telegram_chat_id');
            $table->index('telegram_token');
        });

        // Generate tokens for existing users
        User::whereNull('telegram_token')->each(function ($user) {
            $user->update(['telegram_token' => Str::random(32)]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['telegram_token']);
            $table->dropColumn(['telegram_chat_id', 'telegram_token']);
        });
    }
};
