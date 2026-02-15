<?php

namespace App\Http\Controllers;

use App\Http\Requests\BusinessRequest;
use App\Models\Business;
use App\Models\BusinessRole;
use App\Models\Country;
use App\Services\AdminNotificationService;
use App\Services\SubscriptionService;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Throwable;

class BusinessSettingsController extends Controller
{
    /**
     * Страница создания бизнеса
     */
    public function create()
    {
        $user = Auth::user();
        $hasBusinesses = $user && $user->businesses()->exists();
        if ($hasBusinesses) {
            $role = $this->getCurrentBusinessRole();
            $permissionService = app(\App\Services\BusinessRolePermissionService::class);
            if (! $role || ! $permissionService->hasPermission($role->id, 'client.businesses.create')) {
                abort(403, 'У вас нет прав на создание бизнеса.');
            }
        }

        $subscriptionService = app(SubscriptionService::class);
        if (! $subscriptionService->canCreateBusiness($user)) {
            return redirect()
                ->route('settings.businesses.index')
                ->with('error', SubscriptionService::planLimitErrorMessage());
        }

        return view('settings.business.create', [
            'countries' => Country::getForPhoneSelect(),
        ]);
    }

    /**
     * Создание бизнеса
     *
     * @throws Throwable
     */
    public function store(BusinessRequest $request)
    {
        $user = Auth::user();

        $hasBusinesses = $user && $user->businesses()->exists();
        if ($hasBusinesses) {
            $role = $this->getCurrentBusinessRole();
            $permissionService = app(\App\Services\BusinessRolePermissionService::class);
            if (! $role || ! $permissionService->hasPermission($role->id, 'client.businesses.create')) {
                abort(403, 'У вас нет прав на создание бизнеса.');
            }
        }

        $subscriptionService = app(SubscriptionService::class);
        if (! $subscriptionService->canCreateBusiness($user)) {
            return redirect()->back()
                ->withInput()
                ->with('error', SubscriptionService::planLimitErrorMessage());
        }

        $validated = $request->validated();
        $ownerData = $request->validate(
            [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['nullable', 'string', 'max:255'],
            ],
            [
                'first_name.required' => 'Поле "Имя" обязательно для заполнения.',
                'first_name.max' => 'Поле "Имя" не может быть длиннее 255 символов.',
                'last_name.max' => 'Поле "Фамилия" не может быть длиннее 255 символов.',
            ],
        );

        $phoneCountryId = (int) $validated['phone_country_id'];
        $phoneE164 = $validated['phone'];

        $business = DB::transaction(function () use (
            $ownerData,
            $phoneCountryId,
            $phoneE164,
            $user,
            $validated,
        ) {
            $business = Business::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'type' => $validated['type'] ?? Business::TYPE_ORGANIZATION,
                'owner_id' => $user->id,
                'description' => $validated['description'],
                'online_booking_enabled' => false,
            ]);

            $business->phones()->create([
                'country_id' => $phoneCountryId,
                'phone' => $phoneE164,
                'type' => 'primary',
            ]);

            $ownerRole = BusinessRole::where('slug', 'owner')->first();
            $business->users()->attach($user, [
                'role_id' => $ownerRole?->id,
                'first_name' => $ownerData['first_name'],
                'last_name' => $ownerData['last_name'],
            ]);

            return $business;
        });

        AdminNotificationService::notifyBusinessCreated($business);

        $this->setCurrentBusiness($business);

        return redirect()
            ->route('settings.index')
            ->with('success', 'Бизнес успешно создан!');
    }

    /**
     * Переключить текущий бизнес (установить в сессию и редирект назад).
     */
    public function switch(Request $request)
    {
        $request->validate(['business_id' => ['required', 'integer', 'exists:businesses,id']]);

        $user = Auth::user();
        $business = Business::find($request->business_id);

        if (! $business || ! $user->businesses->contains($business->id)) {
            return redirect()->back()->with('error', 'У вас нет доступа к этому бизнесу.');
        }

        $this->setCurrentBusiness($business);

        return redirect()->back()->with('success', 'Бизнес переключен.');
    }

    /**
     * Страница управления бизнесами (список, переключение, добавление)
     */
    public function businessesIndex()
    {
        $user = Auth::user();
        $userBusinesses = $user ? $user->businesses : collect();
        $business = $this->getCurrentBusiness();

        $subscriptionService = app(SubscriptionService::class);
        $canCreateBusiness = $user && $subscriptionService->canCreateBusiness($user);
        $businessUsage = $user ? $subscriptionService->getCurrentUsage($user, 'max_businesses') : 0;
        $businessLimit = $user ? $subscriptionService->getLimit($user, 'max_businesses') : null;
        if ($businessLimit === true || $businessLimit === null) {
            $businessLimit = -1;
        }

        $permissionService = app(\App\Services\BusinessRolePermissionService::class);
        $role = $this->getCurrentBusinessRole();
        $canUpdateBusiness = $role && $permissionService->hasPermission($role->id, 'client.businesses.update');
        $canDeleteBusiness = $role && $permissionService->hasPermission($role->id, 'client.businesses.delete');
        $canCreateBusinessByPermission = ! $userBusinesses->isNotEmpty()
            || ($role && $permissionService->hasPermission($role->id, 'client.businesses.create'));

        return view('settings.businesses.index', [
            'business' => $business,
            'userBusinesses' => $userBusinesses,
            'canCreateBusiness' => $canCreateBusiness,
            'canCreateBusinessByPermission' => $canCreateBusinessByPermission,
            'businessUsage' => $businessUsage,
            'businessLimit' => $businessLimit,
            'canUpdateBusiness' => $canUpdateBusiness,
            'canDeleteBusiness' => $canDeleteBusiness,
        ]);
    }

    /**
     * Главная страница настроек бизнеса
     */
    public function index()
    {
        $user = Auth::user();
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('settings.businesses.index')
                ->with('info', 'Создайте бизнес');
        }

        $userBusinesses = $user ? $user->businesses : collect();

        $business->load(['locations', 'services', 'masters', 'clients']);

        $bot = \DefStudio\Telegraph\Models\TelegraphBot::first();

        $permissionService = app(\App\Services\BusinessRolePermissionService::class);
        $role = $this->getCurrentBusinessRole();
        $canUpdateBusiness = $role && $permissionService->hasPermission($role->id, 'client.businesses.update');
        $canViewBusinesses = $role && $permissionService->hasPermission($role->id, 'client.businesses.view');

        return view('settings.index', [
            'business' => $business,
            'userBusinesses' => $userBusinesses,
            'bot' => $bot,
            'canUpdateBusiness' => $canUpdateBusiness,
            'canViewBusinesses' => $canViewBusinesses,
        ]);
    }

    /**
     * Страница онлайн-записи
     */
    public function onlineBooking()
    {
        $business = $this->getCurrentBusiness();
        $eligibilityService = app(\App\Services\OnlineBookingEligibilityService::class);
        $eligibility = $eligibilityService->getEligibility($business);

        if (! $business) {
            $bot = \DefStudio\Telegraph\Models\TelegraphBot::first();

            return view('settings.online-booking', [
                'business' => null,
                'bot' => $bot,
                'telegramBotEnabled' => false,
                'can_enable_online_booking' => $eligibility['can_enable'],
                'checks' => $eligibility['checks'],
            ]);
        }

        $bot = \DefStudio\Telegraph\Models\TelegraphBot::first();

        // Проверяем доступ к Telegram боту для записи
        $subscriptionService = app(SubscriptionService::class);
        $ownerRole = BusinessRole::where('slug', 'owner')->first();
        $owner = null;

        if ($ownerRole) {
            $ownerPivot = DB::table('business_user')
                ->where('business_id', $business->id)
                ->where('role_id', $ownerRole->id)
                ->first();

            if ($ownerPivot) {
                $owner = \App\Models\User::find($ownerPivot->user_id);
            }
        }

        // Fallback: если не нашли по роли, берем первого пользователя
        if (! $owner) {
            $owner = $business->users()->first();
        }

        $telegramBotEnabled = false;
        if ($owner) {
            $telegramBotEnabled =
                $subscriptionService->getLimit(
                    $owner,
                    'telegram_bot_enabled',
                ) === true;
        }

        $eligibility = $eligibilityService->getEligibility($business);

        return view('settings.online-booking', [
            'business' => $business,
            'bot' => $bot,
            'telegramBotEnabled' => $telegramBotEnabled,
            'can_enable_online_booking' => $eligibility['can_enable'],
            'checks' => $eligibility['checks'],
        ]);
    }

    /**
     * Обновление настроек онлайн-записи
     */
    public function updateOnlineBooking(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Сначала создайте бизнес или примите приглашение.',
                    ],
                    422,
                );
            }

            return redirect()
                ->route('settings.online-booking')
                ->with('error', 'Сначала создайте бизнес или примите приглашение.');
        }

        $enabled = $request->input('online_booking_enabled');
        $enabled =
            $enabled === '1' ||
            $enabled === 1 ||
            $enabled === true ||
            $enabled === 'true';

        if ($enabled) {
            $eligibilityService = app(\App\Services\OnlineBookingEligibilityService::class);
            if (! $eligibilityService->canEnable($business)) {
                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Выполните все условия для онлайн-записи: бизнес с идентификатором, локация, мастер, услуга и привязка услуги к мастеру (мастер к локации).',
                    ], 422);
                }

                return redirect()
                    ->route('settings.online-booking')
                    ->with('error', 'Выполните все условия для онлайн-записи (чек-лист на странице).');
            }
        }

        $business->update([
            'online_booking_enabled' => $enabled,
        ]);

        // Обновляем модель после сохранения
        $business->refresh();

        if (
            $request->expectsJson() ||
            $request->wantsJson() ||
            $request->ajax()
        ) {
            return response()->json([
                'success' => true,
                'message' => 'Настройки онлайн-записи обновлены',
                'online_booking_enabled' => (bool) $business->online_booking_enabled,
            ]);
        }

        return redirect()
            ->route('settings.online-booking')
            ->with('success', 'Настройки онлайн-записи обновлены');
    }

    /**
     * Генерация QR-кода для онлайн-записи (веб или Telegram)
     */
    public function onlineBookingQr(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            abort(404);
        }

        $type = $request->query('type', 'web');
        $size = (int) $request->query('size', 200);
        $size = $size >= 100 && $size <= 500 ? $size : 200;
        $download = $request->boolean('download');

        $url = null;

        if ($type === 'web') {
            if (empty($business->slug)) {
                abort(404);
            }
            $url = route('public.appointments.show', ['slug' => $business->slug]);
        } elseif ($type === 'telegram') {
            $bot = \DefStudio\Telegraph\Models\TelegraphBot::first();
            if (! $bot || empty($business->slug)) {
                abort(404);
            }
            $url = 'https://t.me/'.$bot->name.'?start='.$business->slug;
        } else {
            abort(400);
        }

        $builder = new Builder(data: $url, size: $size, margin: 10);
        $result = $builder->build();

        $headers = [
            'Content-Type' => $result->getMimeType(),
            'Cache-Control' => 'private, max-age=3600',
        ];
        if ($download) {
            $filename = $type === 'web'
                ? "qr-zapisi-{$business->slug}.png"
                : "qr-telegram-{$business->slug}.png";
            $headers['Content-Disposition'] = 'attachment; filename="'.$filename.'"';
        }

        return response($result->getString(), 200, $headers);
    }

    /**
     * Страница редактирования данных бизнеса
     */
    public function edit()
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return view('settings.business.edit', [
                'business' => null,
                'countries' => Country::getForPhoneSelect(),
                'canDeleteBusiness' => false,
            ]);
        }

        $permissionService = app(\App\Services\BusinessRolePermissionService::class);
        $role = $this->getCurrentBusinessRole();
        $canDeleteBusiness = $role && $permissionService->hasPermission($role->id, 'client.businesses.delete');

        return view('settings.business.edit', [
            'business' => $business,
            'countries' => Country::getForPhoneSelect(),
            'canDeleteBusiness' => $canDeleteBusiness,
        ]);
    }

    /**
     * Обновление данных бизнеса
     */
    public function update(BusinessRequest $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->back()->with('error', 'Сначала создайте бизнес или примите приглашение.');
        }

        $validated = $request->validated();
        $businessData = collect($validated)
            ->only(['name', 'slug', 'type', 'description'])
            ->all();
        $phoneCountryId = (int) $validated['phone_country_id'];
        $phoneE164 = $validated['phone'];

        $business->update($businessData);

        $primary = $business->primaryPhone;
        if ($primary) {
            $primary->update([
                'country_id' => $phoneCountryId,
                'phone' => $phoneE164,
            ]);
        } else {
            $business->phones()->create([
                'country_id' => $phoneCountryId,
                'phone' => $phoneE164,
                'type' => 'primary',
            ]);
        }

        return redirect()
            ->route('settings.index')
            ->with('success', 'Данные бизнеса обновлены');
    }

    /**
     * Удалить текущий бизнес (только владелец). Мягкое удаление.
     */
    public function destroy()
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('settings.index')->with('error', 'Бизнес не найден.');
        }

        $user = Auth::user();
        if ($business->owner_id !== $user->id) {
            abort(403, 'Удалить бизнес может только владелец.');
        }

        $businessId = $business->id;
        $business->delete();

        if (Session::get('current_business_id') == $businessId) {
            Session::forget('current_business_id');
        }

        return redirect()
            ->route('settings.index')
            ->with('success', 'Бизнес удалён.');
    }
}
