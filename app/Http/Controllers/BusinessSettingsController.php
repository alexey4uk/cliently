<?php

namespace App\Http\Controllers;

use App\Http\Requests\BusinessRequest;
use App\Models\Business;
use App\Models\BusinessRole;
use App\Models\Country;
use App\Services\AdminNotificationService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class BusinessSettingsController extends Controller
{
    /**
     * Страница создания бизнеса
     */
    public function create()
    {
        $user = Auth::user();

        // Если у пользователя уже есть бизнес, перенаправляем в настройки
        if ($user->businesses->isNotEmpty()) {
            return redirect()->route('settings.index');
        }

        return view('settings.business.create', [
            'countries' => Country::orderBy('name')->get(),
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

        // Если у пользователя уже есть бизнес, перенаправляем в настройки
        if ($user->businesses->isNotEmpty()) {
            return redirect()->route('settings.index');
        }

        $validated = $request->validated();
        $ownerData = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
        ], [
            'first_name.required' => 'Поле "Имя" обязательно для заполнения.',
            'first_name.max' => 'Поле "Имя" не может быть длиннее 255 символов.',
            'last_name.max' => 'Поле "Фамилия" не может быть длиннее 255 символов.',
        ]);

        $businessData = collect($validated)->only(['name', 'slug', 'description'])->all();
        $phoneCountryId = (int) $validated['phone_country_id'];
        $phoneE164 = $validated['phone'];

        $business = null;
        DB::transaction(function () use ($businessData, $ownerData, $phoneCountryId, $phoneE164, $user, &$business) {
            $business = Business::create($businessData);

            $business->phones()->create([
                'country_id' => $phoneCountryId,
                'phone' => $phoneE164,
                'type' => 'primary',
            ]);

            $ownerRole = BusinessRole::where('slug', 'owner')->first();
            $business->users()->attach($user, [
                'role' => 'owner',
                'role_id' => $ownerRole?->id,
                'first_name' => $ownerData['first_name'],
                'last_name' => $ownerData['last_name'],
            ]);
        });

        AdminNotificationService::notifyBusinessCreated($business);

        return redirect()->route('settings.index')->with('success', 'Бизнес успешно создан!');
    }

    /**
     * Главная страница настроек бизнеса
     */
    public function index()
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $business->load(['locations', 'services', 'masters', 'clients']);

        $bot = \DefStudio\Telegraph\Models\TelegraphBot::first();

        return view('settings.index', [
            'business' => $business,
            'bot' => $bot,
        ]);
    }

    /**
     * Страница онлайн-записи
     */
    public function onlineBooking()
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
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
            $telegramBotEnabled = $subscriptionService->getLimit($owner, 'telegram_bot_enabled') === true;
        }

        return view('settings.online-booking', [
            'business' => $business,
            'bot' => $bot,
            'telegramBotEnabled' => $telegramBotEnabled,
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
                return response()->json([
                    'success' => false,
                    'message' => 'Сначала создайте бизнес или примите приглашение.',
                ], 422);
            }
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $enabled = $request->input('online_booking_enabled');
        $enabled = $enabled === '1' || $enabled === 1 || $enabled === true || $enabled === 'true';

        $business->update([
            'online_booking_enabled' => $enabled,
        ]);

        // Обновляем модель после сохранения
        $business->refresh();

        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Настройки онлайн-записи обновлены',
                'online_booking_enabled' => (bool) $business->online_booking_enabled,
            ]);
        }

        return redirect()->route('settings.online-booking')->with('success', 'Настройки онлайн-записи обновлены');
    }

    /**
     * Страница редактирования данных бизнеса
     */
    public function edit()
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        return view('settings.business.edit', [
            'business' => $business,
            'countries' => Country::orderBy('name')->get(),
        ]);
    }

    /**
     * Обновление данных бизнеса
     */
    public function update(BusinessRequest $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $validated = $request->validated();
        $businessData = collect($validated)->only(['name', 'slug', 'description'])->all();
        $phoneCountryId = (int) $validated['phone_country_id'];
        $phoneE164 = $validated['phone'];

        $business->update($businessData);

        $primary = $business->primaryPhone;
        if ($primary) {
            $primary->update(['country_id' => $phoneCountryId, 'phone' => $phoneE164]);
        } else {
            $business->phones()->create([
                'country_id' => $phoneCountryId,
                'phone' => $phoneE164,
                'type' => 'primary',
            ]);
        }

        return redirect()->route('settings.index')->with('success', 'Данные бизнеса обновлены');
    }
}
