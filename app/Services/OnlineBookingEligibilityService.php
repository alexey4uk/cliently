<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Business;
use Illuminate\Support\Facades\DB;

/**
 * Проверка условий для возможности включения онлайн-записи.
 * Возвращает чек-лист и флаг can_enable.
 */
class OnlineBookingEligibilityService
{
    /**
     * Проверить, можно ли включить онлайн-запись для бизнеса.
     *
     * @return array{can_enable: bool, checks: array{has_business: bool, has_slug: bool, has_locations: bool, has_masters: bool, has_services: bool, has_booking_chain: bool}}
     */
    public function getEligibility(?Business $business): array
    {
        $checks = [
            'has_business' => $business !== null,
            'has_slug' => false,
            'has_locations' => false,
            'has_masters' => false,
            'has_services' => false,
            'has_booking_chain' => false,
        ];

        if (! $business) {
            return [
                'can_enable' => false,
                'checks' => $checks,
            ];
        }

        $checks['has_slug'] = ! empty($business->slug);
        $checks['has_locations'] = $business->locations()->exists();
        $checks['has_masters'] = $business->masters()->where('is_active', true)->exists();
        $checks['has_services'] = $business->services()->where('is_active', true)->exists();

        // Есть хотя бы одна связка: локация + мастер (в локации) + услуга у мастера
        $checks['has_booking_chain'] = $this->hasBookingChain($business);

        $canEnable = $checks['has_business']
            && $checks['has_slug']
            && $checks['has_locations']
            && $checks['has_masters']
            && $checks['has_services']
            && $checks['has_booking_chain'];

        return [
            'can_enable' => $canEnable,
            'checks' => $checks,
        ];
    }

    /**
     * Есть ли хотя бы одна цепочка: локация — мастер в ней — услуга у мастера.
     */
    protected function hasBookingChain(Business $business): bool
    {
        return DB::table('locations')
            ->where('business_id', $business->id)
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('master_location')
                    ->whereColumn('master_location.location_id', 'locations.id')
                    ->whereExists(function ($q2) {
                        $q2->select(DB::raw(1))
                            ->from('service_master')
                            ->whereColumn('service_master.master_id', 'master_location.master_id');
                    });
            })
            ->exists();
    }

    /**
     * Можно ли включить онлайн-запись (удобный метод для бэкенд-проверки).
     */
    public function canEnable(?Business $business): bool
    {
        $eligibility = $this->getEligibility($business);

        return $eligibility['can_enable'];
    }
}
