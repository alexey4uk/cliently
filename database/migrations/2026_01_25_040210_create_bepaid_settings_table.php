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
        Schema::create('bepaid_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('test_mode')->default(true);
            $table->string('test_shop_id')->nullable();
            $table->text('test_secret_key')->nullable();
            $table->string('test_gateway_base')->nullable();
            $table->string('test_checkout_base')->nullable();
            $table->string('production_shop_id')->nullable();
            $table->text('production_secret_key')->nullable();
            $table->string('production_gateway_base')->nullable();
            $table->string('production_checkout_base')->nullable();
            $table->string('webhook_url')->nullable();
            $table->boolean('enabled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bepaid_settings');
    }
};
