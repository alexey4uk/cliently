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
        Schema::table('users', function (Blueprint $table) {
            $table->string('oauth_provider')->nullable()->after('email');
            $table->string('oauth_id')->nullable()->after('oauth_provider');
            $table->string('avatar')->after('oauth_id')->nullable();
            $table->json('dashboard_settings')->nullable()->after('avatar');
            $table->string('telegram_chat_id')->nullable()->after('dashboard_settings');
            $table->string('telegram_token', 64)->unique()->nullable()->after('telegram_chat_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
            $table->index(['oauth_provider', 'oauth_id'], 'oauth_provider_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('oauth_provider_id_index');
            $table->dropColumn([
                'avatar',
                'dashboard_settings',
                'telegram_chat_id',
                'telegram_token',
                'oauth_provider',
                'oauth_id',
            ]);
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
