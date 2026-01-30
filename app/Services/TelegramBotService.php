<?php

namespace App\Services;

use App\Models\Business;
use App\Models\User;
use App\Repositories\AppointmentRepositoryInterface;
use App\Repositories\BusinessRepositoryInterface;
use App\Repositories\ClientRepositoryInterface;
use App\Repositories\LocationRepositoryInterface;
use App\Repositories\MasterRepositoryInterface;
use App\Repositories\ServiceRepositoryInterface;
use App\Repositories\TelegramUserStateRepositoryInterface;
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
     */
    public function findBusinessByToken(string $token): ?Business
    {
        return $this->businessRepository->findByTelegramToken($token);
    }

    /**
     * Найти бизнес по slug
     */
    public function findBusinessBySlug(string $slug): ?Business
    {
        return $this->businessRepository->findBySlug($slug);
    }

    /**
     * Получить пагинированный список бизнесов
     */
    public function getBusinessesPaginated(int $page = 1, int $perPage = 10): Collection
    {
        return $this->businessRepository->getPaginated($page, $perPage);
    }

    /**
     * Получить общее количество бизнесов
     */
    public function getTotalBusinesses(): int
    {
        return $this->businessRepository->getTotalCount();
    }

    /**
     * Поиск бизнесов по названию
     */
    public function searchBusinesses(string $query, int $page = 1, int $perPage = 10): Collection
    {
        return $this->businessRepository->searchByNamePaginated($query, $page, $perPage);
    }

    /**
     * Получить количество результатов поиска
     */
    public function getSearchCount(string $query): int
    {
        return $this->businessRepository->getSearchCount($query);
    }

    /**
     * Обновить чат ID для бизнеса
     */
    public function updateBusinessChatId(Business $business, int $chatId): bool
    {
        return $this->businessRepository->updateTelegramChatId($business, $chatId);
    }

    /**
     * Получить локации для бизнеса
     */
    public function getLocationsForBusiness(int $businessId): Collection
    {
        return $this->locationRepository->getByBusiness($businessId);
    }

    /**
     * Получить услуги для бизнеса
     */
    public function getServicesForBusiness(int $businessId): Collection
    {
        return $this->serviceRepository->getActiveByBusiness($businessId);
    }

    /**
     * Получить мастеров для локации и услуги
     */
    public function getMastersForLocationAndService(int $locationId, ?int $serviceId = null): Collection
    {
        return $this->masterRepository->getActiveByLocation($locationId, $serviceId);
    }

    /**
     * Получить мастеров для бизнеса и услуги
     */
    public function getMastersForBusinessAndService(int $businessId, ?int $serviceId = null): Collection
    {
        return $this->masterRepository->getActiveByBusiness($businessId, $serviceId);
    }

    /**
     * Найти или создать клиента
     *
     * @return \App\Models\Client
     */
    public function findOrCreateClient(int $businessId, string $phone, array $attributes = [])
    {
        return $this->clientRepository->firstOrCreateByPhone($businessId, $phone, $attributes);
    }

    /**
     * Создать запись на прием
     *
     * @return \App\Models\Appointment
     */
    public function createAppointment(array $data)
    {
        return $this->appointmentRepository->createAppointment($data);
    }

    /**
     * Получить запись с отношениями
     *
     * @return \App\Models\Appointment|null
     */
    public function getAppointmentWithRelations(int $id, array $relations = ['client', 'service', 'master', 'location'])
    {
        return $this->appointmentRepository->findWithRelations($id, $relations);
    }

    /**
     * Получить текущее состояние пользователя
     *
     * @return \App\Models\TelegramUserState|null
     */
    public function getCurrentUserState(string $telegramUserId)
    {
        return $this->stateRepository->getCurrentState($telegramUserId);
    }

    /**
     * Получить состояние для бизнеса
     *
     * @return \App\Models\TelegramUserState|null
     */
    public function getUserState(string $telegramUserId, ?int $businessId)
    {
        return $this->stateRepository->getState($telegramUserId, $businessId);
    }

    /**
     * Обновить состояние пользователя
     *
     * @return \App\Models\TelegramUserState
     */
    public function updateUserState(string $telegramUserId, ?int $businessId, string $step, array $data = [])
    {
        return $this->stateRepository->updateState($telegramUserId, $businessId, $step, $data);
    }

    /**
     * Обновить состояние, сохраняя message_id
     *
     * @return \App\Models\TelegramUserState
     */
    public function updateUserStateKeepMessageId(string $telegramUserId, ?int $businessId, string $step, array $data = [])
    {
        return $this->stateRepository->updateStateKeepMessageId($telegramUserId, $businessId, $step, $data);
    }

    /**
     * Очистить состояние пользователя
     */
    public function clearUserState(string $telegramUserId, ?int $businessId = null): bool
    {
        return $this->stateRepository->clearState($telegramUserId, $businessId);
    }

    /**
     * Сохранить ID последнего сообщения
     */
    public function setUserMessageId(string $telegramUserId, ?int $businessId, int $messageId): void
    {
        $this->stateRepository->setMessageId($telegramUserId, $businessId, $messageId);
    }

    /**
     * Получить ID последнего сообщения
     */
    public function getUserMessageId(string $telegramUserId, ?int $businessId): ?int
    {
        return $this->stateRepository->getMessageId($telegramUserId, $businessId);
    }

    /**
     * Найти локацию по ID
     */
    public function findLocation(int $id): ?\App\Models\Location
    {
        return $this->locationRepository->find($id);
    }

    /**
     * Найти услугу по ID
     */
    public function findService(int $id): ?\App\Models\Service
    {
        return $this->serviceRepository->find($id);
    }

    /**
     * Найти мастера по ID
     */
    public function findMaster(int $id): ?\App\Models\Master
    {
        return $this->masterRepository->find($id);
    }

    /**
     * Найти пользователя по токену Telegram
     */
    public function findUserByToken(string $token): ?User
    {
        return User::where('telegram_token', $token)->first();
    }

    /**
     * Обновить чат ID для пользователя
     */
    public function updateUserChatId(User $user, int $chatId): bool
    {
        $user->telegram_chat_id = (string) $chatId;

        return $user->save();
    }
}
