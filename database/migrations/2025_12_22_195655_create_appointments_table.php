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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique()->nullable();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('master_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->time('time');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->string('source')->nullable();
            $table->text('notes')->nullable();
            $table->integer('duration')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['date', 'time']);
            $table->index('created_at', 'appointments_created_at_index');
            $table->index(['master_id', 'date'], 'appointments_master_date');
            $table->index(['business_id', 'status', 'date'], 'appointments_business_status_date');
            $table->index(['created_at', 'business_id'], 'appointments_created_at_business_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
