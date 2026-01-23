<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('business_user')) {
            DB::statement("ALTER TABLE `business_user` MODIFY `role` VARCHAR(100) NOT NULL DEFAULT 'master'");
        }

        if (Schema::hasTable('business_user_invitations')) {
            DB::statement("ALTER TABLE `business_user_invitations` MODIFY `role` VARCHAR(100) NOT NULL");
        }

        if (Schema::hasTable('business_role_permissions')) {
            DB::statement("ALTER TABLE `business_role_permissions` MODIFY `role` VARCHAR(100) NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('business_user')) {
            DB::statement("ALTER TABLE `business_user` MODIFY `role` ENUM('owner','admin','master') NOT NULL DEFAULT 'master'");
        }

        if (Schema::hasTable('business_user_invitations')) {
            DB::statement("ALTER TABLE `business_user_invitations` MODIFY `role` ENUM('owner','admin','master') NOT NULL");
        }

        if (Schema::hasTable('business_role_permissions')) {
            DB::statement("ALTER TABLE `business_role_permissions` MODIFY `role` ENUM('owner','admin','master') NOT NULL");
        }
    }
};
