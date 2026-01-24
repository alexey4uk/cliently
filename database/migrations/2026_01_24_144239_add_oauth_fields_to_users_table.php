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
            // OAuth provider name (google, vk, yandex, telegram, etc.)
            $table->string('oauth_provider')->nullable()->after('email');
            
            // OAuth provider user ID
            $table->string('oauth_id')->nullable()->after('oauth_provider');
            
            // Make email and password nullable for OAuth users
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
            
            // Add index for faster OAuth user lookup
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
            $table->dropColumn(['oauth_provider', 'oauth_id']);
            
            // Restore email and password to NOT NULL (be careful in production!)
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
