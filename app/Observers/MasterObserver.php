<?php

namespace App\Observers;

use App\Models\Master;
use Illuminate\Support\Facades\DB;

class MasterObserver
{
    /**
     * Handle the Master "deleting" event.
     * Выполняется перед удалением мастера.
     */
    public function deleting(Master $master): void
    {
        // Очищаем master_id в business_user для всех пользователей, связанных с этим мастером
        DB::table('business_user')
            ->where('master_id', $master->id)
            ->update(['master_id' => null]);

        // Очищаем master_id в business_user_invitations для всех приглашений, связанных с этим мастером
        DB::table('business_user_invitations')
            ->where('master_id', $master->id)
            ->update(['master_id' => null]);
    }

    /**
     * Handle the Master "deleted" event.
     * Выполняется после удаления мастера.
     */
    public function deleted(Master $master): void
    {
        // Логирование или другие действия после удаления
        // Записи (appointments) остаются с master_id = null благодаря nullOnDelete()
    }
}
