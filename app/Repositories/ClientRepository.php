<?php

namespace App\Repositories;

use App\Models\Client;

/**
 * Репозиторий для работы с клиентами
 */
class ClientRepository extends BaseRepository implements ClientRepositoryInterface
{
    /**
     * Получить модель для репозитория
     *
     * @return Client
     */
    public function getModel(): Client
    {
        return new Client();
    }

    /**
     * Найти или создать клиента по телефону и бизнесу
     *
     * @param int $businessId
     * @param string $phone
     * @param array $attributes
     * @return Client
     */
    public function firstOrCreateByPhone(int $businessId, string $phone, array $attributes = []): Client
    {
        $defaultAttributes = array_merge($attributes, [
            'business_id' => $businessId,
            'phone' => $phone,
        ]);

        return $this->model->firstOrCreate(
            [
                'business_id' => $businessId,
                'phone' => $phone,
            ],
            $defaultAttributes
        );
    }

    /**
     * Найти клиента по телефону и бизнесу
     *
     * @param int $businessId
     * @param string $phone
     * @return Client|null
     */
    public function findByPhone(int $businessId, string $phone): ?Client
    {
        return $this->model->where('business_id', $businessId)
            ->where('phone', $phone)
            ->first();
    }

    /**
     * Получить клиентов бизнеса
     *
     * @param int $businessId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByBusiness(int $businessId)
    {
        return $this->model->where('business_id', $businessId)->get();
    }
}
