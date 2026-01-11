<?php

namespace App\Traits;

trait HasOldValues
{
    protected function getOldValue(string $fieldName, $default = null)
    {
        if (session()->has('errors')) {
            $oldValue = old($fieldName);

            if ($oldValue !== null) {
                return $oldValue;
            }
        }

        return $default;
    }
}
