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
        Schema::create('business_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained('businesses')->cascadeOnDelete();
            $table->string('role', 100);
            $table->string('permission', 255);
            $table->boolean('granted')->default(true);
            $table->timestamps();

            // Unique index on (business_id, role, permission) - NULL values are considered distinct
            $table->unique(['business_id', 'role', 'permission'], 'business_role_permission_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_role_permissions');
    }
};
