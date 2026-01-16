<?php

use App\Models\Business;
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
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('telegram_token', 64)->unique()->nullable();
        });

        // Generate tokens for existing businesses
        Business::all()->each(function ($business) {
            $business->update(['telegram_token' => Str::random(32)]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('telegram_token');
        });
    }
};
