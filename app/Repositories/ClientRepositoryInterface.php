<?php

namespace App\Repositories;

use App\Models\Client;

/**
 * Интерфейс для репозитория Client
 */
interface ClientRepositoryInterface extends RepositoryInterface
{
    /**
     * Найти или создать клиента по телефону и бизнесу
     *
     * @param int $businessId
     * @param string $phone
     * @param array $attributes
     * @return Client
     */
    public function firstOrCreateByPhone(int $businessId, string $phone, array $attributes = []): Client;

    /**
     * Найти клиента по телефону и бизнесу
     *
     * @param int $businessId
     * @param string $phone
     * @return Client|null
     */
    public function findByPhone(int $businessId, string $phone): ?Client;

    /**
     * Получить клиентов бизнеса
     *
     * @param int $businessId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByBusiness(int $businessId);
}
