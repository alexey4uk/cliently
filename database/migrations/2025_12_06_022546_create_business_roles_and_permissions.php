<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('slug', 100);
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
            $table->unique(['slug', 'owner_id'], 'br_slug_owner_unique');
        });

        Schema::create('business_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('business_roles')->cascadeOnDelete();
            $table->string('permission', 255);
            $table->boolean('granted')->default(true);
            $table->timestamps();
            $table->unique(['role_id', 'permission'], 'brp_role_perm_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_role_permissions');
        Schema::dropIfExists('business_roles');
    }
};
