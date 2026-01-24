<?php

namespace App\Http\Controllers;

use App\Http\Requests\BusinessRequest;
use App\Models\Business;
use App\Models\BusinessRole;
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

        return view('settings.business.create');
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

        $businessData = $request->validated();

        // Дополнительная валидация для имени и фамилии владельца
        $ownerData = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
        ], [
            'first_name.required' => 'Поле "Имя" обязательно для заполнения.',
            'first_name.max' => 'Поле "Имя" не может быть длиннее 255 символов.',
            'last_name.max' => 'Поле "Фамилия" не может быть длиннее 255 символов.',
        ]);

        DB::transaction(function () use ($businessData, $ownerData, $user) {
            $business = Business::create($businessData);
            $ownerRole = BusinessRole::where('slug', 'owner')->first();

            $business->users()->attach($user, [
                'role' => 'owner',
                'role_id' => $ownerRole?->id,
                'first_name' => $ownerData['first_name'],
                'last_name' => $ownerData['last_name'],
            ]);
        });

        return redirect()->route('settings.index')->with('success', 'Бизнес успешно создан!');
    }

    /**
     * Главная страница настроек бизнеса
     */
    public function index()
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
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

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $bot = \DefStudio\Telegraph\Models\TelegraphBot::first();

        return view('settings.online-booking', [
            'business' => $business,
            'bot' => $bot,
        ]);
    }

    /**
     * Обновление настроек онлайн-записи
     */
    public function updateOnlineBooking(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $business->update([
            'online_booking_enabled' => $request->boolean('online_booking_enabled'),
        ]);

        return redirect()->route('settings.online-booking')->with('success', 'Настройки онлайн-записи обновлены');
    }

    /**
     * Страница редактирования данных бизнеса
     */
    public function edit()
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        return view('settings.business.edit', [
            'business' => $business,
        ]);
    }

    /**
     * Обновление данных бизнеса
     */
    public function update(BusinessRequest $request)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $business->update($request->validated());

        return redirect()->route('settings.index')->with('success', 'Данные бизнеса обновлены');
    }
}
