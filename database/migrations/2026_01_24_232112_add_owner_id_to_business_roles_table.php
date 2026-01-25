<?php

use App\Models\BusinessRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('business_roles', function (Blueprint $table) {
            // Добавляем owner_id (nullable, foreign key на users)
            $table->foreignId('owner_id')->nullable()->after('is_system')->constrained('users')->nullOnDelete();
        });

        // Удаляем текущий UNIQUE индекс на slug
        Schema::table('business_roles', function (Blueprint $table) {
            $table->dropUnique(['slug']);
        });

        // Создаем составной уникальный индекс: (slug, owner_id) для пользовательских ролей
        // MySQL не поддерживает partial indexes, поэтому используем составной индекс
        Schema::table('business_roles', function (Blueprint $table) {
            $table->unique(['slug', 'owner_id'], 'business_roles_slug_owner_id_unique');
        });

        // Обновляем существующие системные роли: устанавливаем owner_id = NULL
        DB::table('business_roles')
            ->where('is_system', true)
            ->update(['owner_id' => null]);

        // Обновляем существующие пользовательские роли: определяем owner на основе использования
        $this->migrateCustomRoles();
    }

    /**
     * Миграция данных для пользовательских ролей
     */
    private function migrateCustomRoles(): void
    {
        // Получаем системную роль owner
        $ownerRole = BusinessRole::where('slug', 'owner')->first();

        if (! $ownerRole) {
            return;
        }

        // Получаем все пользовательские роли
        $customRoles = BusinessRole::where('is_system', false)->get();

        foreach ($customRoles as $role) {
            // Находим все бизнесы, где используется эта роль
            $businessIds = DB::table('business_user')
                ->where('role_id', $role->id)
                ->distinct()
                ->pluck('business_id')
                ->toArray();

            if (empty($businessIds)) {
                // Роль не используется - можно оставить owner_id = NULL или удалить
                // Оставляем NULL для возможности использования в будущем
                continue;
            }

            // Для каждого бизнеса находим owner
            $ownerIds = [];
            foreach ($businessIds as $businessId) {
                $ownerPivot = DB::table('business_user')
                    ->where('business_id', $businessId)
                    ->where('role_id', $ownerRole->id)
                    ->first();

                if ($ownerPivot) {
                    $ownerIds[] = $ownerPivot->user_id;
                }
            }

            $ownerIds = array_unique($ownerIds);

            if (count($ownerIds) === 0) {
                // Не удалось определить owner - оставляем NULL
                continue;
            } elseif (count($ownerIds) === 1) {
                // Роль используется в бизнесах одного owner - привязываем к этому owner
                $role->update(['owner_id' => $ownerIds[0]]);
            } else {
                // Роль используется в бизнесах разных owners - создаем копии для каждого owner
                $firstOwnerId = array_shift($ownerIds);

                // Обновляем текущую роль для первого owner
                $role->update(['owner_id' => $firstOwnerId]);

                // Создаем копии роли для остальных owners
                foreach ($ownerIds as $ownerId) {
                    // Создаем новую роль с тем же slug, но другим owner_id
                    // Уникальный индекс (slug, owner_id) позволяет иметь одинаковый slug для разных owners
                    $newRole = BusinessRole::create([
                        'slug' => $role->slug,
                        'name' => $role->name,
                        'description' => $role->description,
                        'is_system' => false,
                        'owner_id' => $ownerId,
                    ]);

                    // Копируем права
                    $permissions = DB::table('business_role_permissions')
                        ->where('role_id', $role->id)
                        ->get();

                    foreach ($permissions as $permission) {
                        DB::table('business_role_permissions')->insert([
                            'role_id' => $newRole->id,
                            'permission' => $permission->permission,
                            'granted' => $permission->granted,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    // Находим бизнесы этого owner
                    $ownerBusinessIds = [];
                    foreach ($businessIds as $businessId) {
                        $ownerPivot = DB::table('business_user')
                            ->where('business_id', $businessId)
                            ->where('role_id', $ownerRole->id)
                            ->where('user_id', $ownerId)
                            ->first();

                        if ($ownerPivot) {
                            $ownerBusinessIds[] = $businessId;
                        }
                    }

                    // Обновляем role_id в business_user для бизнесов этого owner
                    DB::table('business_user')
                        ->whereIn('business_id', $ownerBusinessIds)
                        ->where('role_id', $role->id)
                        ->update(['role_id' => $newRole->id]);

                    // Обновляем role_id в business_user_invitations для бизнесов этого owner
                    DB::table('business_user_invitations')
                        ->whereIn('business_id', $ownerBusinessIds)
                        ->where('role_id', $role->id)
                        ->update(['role_id' => $newRole->id]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_roles', function (Blueprint $table) {
            // Удаляем составной индекс
            $table->dropUnique('business_roles_slug_owner_id_unique');

            // Восстанавливаем уникальный индекс на slug
            $table->unique('slug');
        });

        Schema::table('business_roles', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropColumn('owner_id');
        });
    }
};
