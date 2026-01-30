<?php

namespace App\Observers;

use App\Models\BepaidSettings;

class BepaidSettingsObserver
{
    /**
     * Handle the BepaidSettings "saved" event.
     */
    public function saved(BepaidSettings $bepaidSettings): void
    {
        BepaidSettings::clearCache();
    }

    /**
     * Handle the BepaidSettings "deleted" event.
     */
    public function deleted(BepaidSettings $bepaidSettings): void
    {
        BepaidSettings::clearCache();
    }
}
