<?php

namespace App\Services;

use App\Models\Business;
use App\Repositories\AppointmentRepositoryInterface;
use App\Repositories\BusinessRepositoryInterface;
use App\Repositories\ClientRepositoryInterface;
use App\Repositories\LocationRepositoryInterface;
use App\Repositories\MasterRepositoryInterface;
use App\Repositories\ServiceRepositoryInterface;
use App\Repositories\TelegramUserStateRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Сервис для бизнес-логики Telegram бота
 */
class TelegramBotService
{
    private BusinessRepositoryInterface $businessRepository;
    private ClientRepositoryInterface $clientRepository;
    private ServiceRepositoryInterface $serviceRepository;
    private LocationRepositoryInterface $locationRepository;
    private MasterRepositoryInterface $masterRepository;
    private AppointmentRepositoryInterface $appointmentRepository;
    private TelegramUserStateRepositoryInterface $stateRepository;

    public function __construct(
        BusinessRepositoryInterface $businessRepository,
        ClientRepositoryInterface $clientRepository,
        ServiceRepositoryInterface $serviceRepository,
        LocationRepositoryInterface $locationRepository,
        MasterRepositoryInterface $masterRepository,
        AppointmentRepositoryInterface $appointmentRepository,
        TelegramUserStateRepositoryInterface $stateRepository
    ) {
        $this->businessRepository = $businessRepository;
        $this->clientRepository = $clientRepository;
        $this->serviceRepository = $serviceRepository;
        $this->locationRepository = $locationRepository;
        $this->masterRepository = $masterRepository;
        $this->appointmentRepository = $appointmentRepository;
        $this->stateRepository = $stateRepository;
    }

    /**
     * Найти бизнес по токену
     *
     * @param string $token
     * @return Business|null
     */
    public function findBusinessByToken(string $token): ?Business
    {
        return $this->businessRepository->findByTelegramToken($token);
    }

    /**
     * Найти бизнес по slug
     *
     * @param string $slug
     * @return Business|null
     */
    public function findBusinessBySlug(string $slug): ?Business
    {
        return $this->businessRepository->findBySlug($slug);
    }

    /**
     * Получить пагинированный список бизнесов
     *
     * @param int $page
     * @param int $perPage
     * @return Collection
     */
    public function getBusinessesPaginated(int $page = 1, int $perPage = 10): Collection
    {
        return $this->businessRepository->getPaginated($page, $perPage);
    }

    /**
     * Получить общее количество бизнесов
     *
     * @return int
     */
    public function getTotalBusinesses(): int
    {
        return $this->businessRepository->getTotalCount();
    }

    /**
     * Поиск бизнесов по названию
     *
     * @param string $query
     * @param int $page
     * @param int $perPage
     * @return Collection
     */
    public function searchBusinesses(string $query, int $page = 1, int $perPage = 10): Collection
    {
        return $this->businessRepository->searchByNamePaginated($query, $page, $perPage);
    }

    /**
     * Получить количество результатов поиска
     *
     * @param string $query
     * @return int
     */
    public function getSearchCount(string $query): int
    {
        return $this->businessRepository->getSearchCount($query);
    }

    /**
     * Обновить чат ID для бизнеса
     *
     * @param Business $business
     * @param int $chatId
     * @return bool
     */
    public function updateBusinessChatId(Business $business, int $chatId): bool
    {
        return $this->businessRepository->updateTelegramChatId($business, $chatId);
    }

    /**
     * Получить локации для бизнеса
     *
     * @param int $businessId
     * @return Collection
     */
    public function getLocationsForBusiness(int $businessId): Collection
    {
        return $this->locationRepository->getByBusiness($businessId);
    }

    /**
     * Получить услуги для локации
     *
     * @param int $locationId
     * @return Collection
     */
    public function getServicesForLocation(int $locationId): Collection
    {
        return $this->serviceRepository->getActiveByLocation($locationId);
    }

    /**
     * Получить услуги для бизнеса
     *
     * @param int $businessId
     * @return Collection
     */
    public function getServicesForBusiness(int $businessId): Collection
    {
        return $this->serviceRepository->getActiveByBusiness($businessId);
    }

    /**
     * Получить мастеров для локации и услуги
     *
     * @param int $locationId
     * @param int|null $serviceId
     * @return Collection
     */
    public function getMastersForLocationAndService(int $locationId, ?int $serviceId = null): Collection
    {
        return $this->masterRepository->getActiveByLocation($locationId, $serviceId);
    }

    /**
     * Получить мастеров для бизнеса и услуги
     *
     * @param int $businessId
     * @param int|null $serviceId
     * @return Collection
     */
    public function getMastersForBusinessAndService(int $businessId, ?int $serviceId = null): Collection
    {
        return $this->masterRepository->getActiveByBusiness($businessId, $serviceId);
    }

    /**
     * Найти или создать клиента
     *
     * @param int $businessId
     * @param string $phone
     * @param array $attributes
     * @return \App\Models\Client
     */
    public function findOrCreateClient(int $businessId, string $phone, array $attributes = [])
    {
        return $this->clientRepository->firstOrCreateByPhone($businessId, $phone, $attributes);
    }

    /**
     * Создать запись на прием
     *
     * @param array $data
     * @return \App\Models\Appointment
     */
    public function createAppointment(array $data)
    {
        return $this->appointmentRepository->createAppointment($data);
    }

    /**
     * Получить запись с отношениями
     *
     * @param int $id
     * @param array $relations
     * @return \App\Models\Appointment|null
     */
    public function getAppointmentWithRelations(int $id, array $relations = ['client', 'service', 'master', 'location'])
    {
        return $this->appointmentRepository->findWithRelations($id, $relations);
    }

    /**
     * Получить текущее состояние пользователя
     *
     * @param string $telegramUserId
     * @return \App\Models\TelegramUserState|null
     */
    public function getCurrentUserState(string $telegramUserId)
    {
        return $this->stateRepository->getCurrentState($telegramUserId);
    }

    /**
     * Получить состояние для бизнеса
     *
     * @param string $telegramUserId
     * @param int|null $businessId
     * @return \App\Models\TelegramUserState|null
     */
    public function getUserState(string $telegramUserId, ?int $businessId)
    {
        return $this->stateRepository->getState($telegramUserId, $businessId);
    }

    /**
     * Обновить состояние пользователя
     *
     * @param string $telegramUserId
     * @param int|null $businessId
     * @param string $step
     * @param array $data
     * @return \App\Models\TelegramUserState
     */
    public function updateUserState(string $telegramUserId, ?int $businessId, string $step, array $data = [])
    {
        return $this->stateRepository->updateState($telegramUserId, $businessId, $step, $data);
    }

    /**
     * Обновить состояние, сохраняя message_id
     *
     * @param string $telegramUserId
     * @param int|null $businessId
     * @param string $step
     * @param array $data
     * @return \App\Models\TelegramUserState
     */
    public function updateUserStateKeepMessageId(string $telegramUserId, ?int $businessId, string $step, array $data = [])
    {
        return $this->stateRepository->updateStateKeepMessageId($telegramUserId, $businessId, $step, $data);
    }

    /**
     * Очистить состояние пользователя
     *
     * @param string $telegramUserId
     * @param int|null $businessId
     * @return bool
     */
    public function clearUserState(string $telegramUserId, ?int $businessId = null): bool
    {
        return $this->stateRepository->clearState($telegramUserId, $businessId);
    }

    /**
     * Сохранить ID последнего сообщения
     *
     * @param string $telegramUserId
     * @param int|null $businessId
     * @param int $messageId
     */
    public function setUserMessageId(string $telegramUserId, ?int $businessId, int $messageId): void
    {
        $this->stateRepository->setMessageId($telegramUserId, $businessId, $messageId);
    }

    /**
     * Получить ID последнего сообщения
     *
     * @param string $telegramUserId
     * @param int|null $businessId
     * @return int|null
     */
    public function getUserMessageId(string $telegramUserId, ?int $businessId): ?int
    {
        return $this->stateRepository->getMessageId($telegramUserId, $businessId);
    }

    /**
     * Найти локацию по ID
     *
     * @param int $id
     * @return \App\Models\Location|null
     */
    public function findLocation(int $id): ?\App\Models\Location
    {
        return $this->locationRepository->find($id);
    }

    /**
     * Найти услугу по ID
     *
     * @param int $id
     * @return \App\Models\Service|null
     */
    public function findService(int $id): ?\App\Models\Service
    {
        return $this->serviceRepository->find($id);
    }

    /**
     * Найти мастера по ID
     *
     * @param int $id
     * @return \App\Models\Master|null
     */
    public function findMaster(int $id): ?\App\Models\Master
    {
        return $this->masterRepository->find($id);
    }
}
