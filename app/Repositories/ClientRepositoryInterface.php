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
     */
    public function firstOrCreateByPhone(int $businessId, string $phone, array $attributes = []): Client;

    /**
     * Найти клиента по телефону и бизнесу
     */
    public function findByPhone(int $businessId, string $phone): ?Client;

    /**
     * Получить клиентов бизнеса
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByBusiness(int $businessId);

    /**
     * Получить недавних клиентов для дашборда
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentForDashboard(int $businessId, int $limit = 5);

    /**
     * Получить количество новых клиентов за период
     */
    public function getNewClientsCount(int $businessId, string $since): int;

    /**
     * Найти клиента по ID и проверить принадлежность бизнесу
     *
     * @return \App\Models\Client|null
     */
    public function findByIdAndBusiness(int $clientId, int $businessId);

    /**
     * Проверить, принадлежит ли клиент бизнесу
     */
    public function belongsToBusiness(int $clientId, int $businessId): bool;
}
