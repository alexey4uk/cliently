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
        Schema::create('ticket_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(true);
            $table->boolean('auto_assign_enabled')->default(false);
            $table->foreignId('auto_assign_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('sla_response_time')->nullable()->comment('Время ответа в минутах');
            $table->boolean('public_form_enabled')->default(true);
            $table->json('public_form_required_fields')->nullable();
            $table->boolean('email_notifications_enabled')->default(true);
            $table->json('email_notification_recipients')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_settings');
    }
};
