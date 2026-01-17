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

    /**
     * Получить недавних клиентов для дашборда
     *
     * @param int $businessId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentForDashboard(int $businessId, int $limit = 5);

    /**
     * Получить количество новых клиентов за период
     *
     * @param int $businessId
     * @param string $since
     * @return int
     */
    public function getNewClientsCount(int $businessId, string $since): int;

    /**
     * Найти клиента по ID и проверить принадлежность бизнесу
     *
     * @param int $clientId
     * @param int $businessId
     * @return \App\Models\Client|null
     */
    public function findByIdAndBusiness(int $clientId, int $businessId);

    /**
     * Проверить, принадлежит ли клиент бизнесу
     *
     * @param int $clientId
     * @param int $businessId
     * @return bool
     */
    public function belongsToBusiness(int $clientId, int $businessId): bool;
}
