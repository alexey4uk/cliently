<?php

namespace App\Repositories;

use App\Models\Client;
use Carbon\Carbon;

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

    /**
     * Получить недавних клиентов для дашборда
     *
     * @param int $businessId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentForDashboard(int $businessId, int $limit = 5)
    {
        return $this->model->where('business_id', $businessId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Получить количество новых клиентов за период
     *
     * @param int $businessId
     * @param string $since
     * @return int
     */
    public function getNewClientsCount(int $businessId, string $since): int
    {
        return $this->model->where('business_id', $businessId)
            ->where('created_at', '>=', $since)
            ->count();
    }

    /**
     * Найти клиента по ID и проверить принадлежность бизнесу
     *
     * @param int $clientId
     * @param int $businessId
     * @return \App\Models\Client|null
     */
    public function findByIdAndBusiness(int $clientId, int $businessId)
    {
        return $this->model->where('id', $clientId)
            ->where('business_id', $businessId)
            ->first();
    }

    /**
     * Проверить, принадлежит ли клиент бизнесу
     *
     * @param int $clientId
     * @param int $businessId
     * @return bool
     */
    public function belongsToBusiness(int $clientId, int $businessId): bool
    {
        return $this->model->where('id', $clientId)
            ->where('business_id', $businessId)
            ->exists();
    }
}
