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
     */
    public function getModel(): Client
    {
        return new Client;
    }

    /**
     * Найти или создать клиента по телефону и бизнесу.
     * Телефон хранится в clients.phone и clients.phone_country_code (ISO).
     */
    public function firstOrCreateByPhone(int $businessId, string $phone, array $attributes = []): Client
    {
        $client = $this->findByPhone($businessId, $phone);

        if ($client) {
            return $client;
        }

        $phoneCountryCode = $attributes['phone_country_code'] ?? null;
        if (! $phoneCountryCode && ! empty($attributes['phone_country_id'])) {
            $phoneCountryCode = \App\Models\Country::find($attributes['phone_country_id'])?->code;
        }
        $phoneCountryCode = $phoneCountryCode ? strtoupper(substr($phoneCountryCode, 0, 2)) : (\App\Models\Country::where('code', 'BY')->value('code') ?? 'BY');
        unset($attributes['phone_country_id'], $attributes['phone_country_code']);
        $payload = array_merge($attributes, [
            'business_id' => $businessId,
            'phone' => $phone,
            'phone_country_code' => $phoneCountryCode,
        ]);
        $client = $this->model->create($payload);

        return $client;
    }

    /**
     * Найти клиента по телефону (E.164) и бизнесу: сначала по колонке clients.phone, затем по morph phones.
     */
    public function findByPhone(int $businessId, string $phone): ?Client
    {
        $client = $this->model->where('business_id', $businessId)->where('phone', $phone)->first();
        if ($client) {
            return $client;
        }

        return $this->model->where('business_id', $businessId)
            ->whereHas('phones', fn ($q) => $q->where('phone', $phone))
            ->first();
    }

    /**
     * Получить клиентов бизнеса
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByBusiness(int $businessId)
    {
        return $this->model->where('business_id', $businessId)->get();
    }

    /**
     * Получить недавних клиентов для дашборда
     *
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
     */
    public function belongsToBusiness(int $clientId, int $businessId): bool
    {
        return $this->model->where('id', $clientId)
            ->where('business_id', $businessId)
            ->exists();
    }
}
