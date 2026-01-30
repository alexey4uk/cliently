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
        Schema::create('phones', function (Blueprint $table) {
            $table->id();
            $table->string('phoneable_type');
            $table->unsignedBigInteger('phoneable_id');
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('phone', 20);
            $table->string('type', 20)->default('primary');
            $table->timestamps();

            $table->fullText('phone', 'ft_phones_number_lookup');
            $table->index(['phoneable_type', 'phoneable_id'], 'idx_phones_poly_owner');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phones');
    }
};
