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
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->foreignId('master_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->time('time');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->string('source')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->integer('duration')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->timestamps();

            $table->index(['master_id', 'date', 'time'], 'idx_appointments_master_schedule');
            $table->index(['business_id', 'status', 'date'], 'idx_appointments_business_flow');
            $table->index(['business_id', 'created_at'], 'idx_appointments_business_created');
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
