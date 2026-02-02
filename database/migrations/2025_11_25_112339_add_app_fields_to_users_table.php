<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone', 20)->nullable()->unique()->after('email');
            $table->string('oauth_provider')->nullable()->after('password');
            $table->string('oauth_id')->nullable()->after('oauth_provider');
            $table->string('avatar')->nullable()->after('oauth_id');
            $table->string('telegram_chat_id')->nullable()->after('avatar');
            $table->string('telegram_token', 64)->unique()->nullable()->after('telegram_chat_id');

            $table->index(['first_name', 'last_name'], 'idx_users_full_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_full_name');
            $table->dropColumn([
                'first_name',
                'last_name',
                'phone',
                'oauth_provider',
                'oauth_id',
                'avatar',
                'telegram_chat_id',
                'telegram_token',
            ]);
        });
    }
};
