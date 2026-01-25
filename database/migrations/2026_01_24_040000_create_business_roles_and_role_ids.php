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
        Schema::create('business_roles', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique();
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::dropIfExists('business_role_permissions');
        Schema::create('business_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('business_roles');
            $table->string('permission', 255);
            $table->boolean('granted')->default(true);
            $table->timestamps();

            $table->unique(['role_id', 'permission'], 'business_role_permission_unique');
        });

        Schema::table('business_user', function (Blueprint $table) {
            if (! Schema::hasColumn('business_user', 'role_id')) {
                $table->foreignId('role_id')->nullable()->constrained('business_roles');
            }
        });

        Schema::table('business_user_invitations', function (Blueprint $table) {
            if (! Schema::hasColumn('business_user_invitations', 'role_id')) {
                $table->foreignId('role_id')->nullable()->constrained('business_roles');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_user_invitations', function (Blueprint $table) {
            if (Schema::hasColumn('business_user_invitations', 'role_id')) {
                $table->dropConstrainedForeignId('role_id');
            }
        });

        Schema::table('business_user', function (Blueprint $table) {
            if (Schema::hasColumn('business_user', 'role_id')) {
                $table->dropConstrainedForeignId('role_id');
            }
        });

        Schema::dropIfExists('business_role_permissions');
        Schema::dropIfExists('business_roles');
    }
};
