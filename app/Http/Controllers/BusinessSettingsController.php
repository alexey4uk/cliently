<?php

namespace App\Http\Controllers;

use App\Http\Requests\BusinessRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessSettingsController extends Controller
{
    /**
     * Главная страница настроек бизнеса
     */
    public function index()
    {
        $user = Auth::user()->load(['businesses.locations', 'businesses.services', 'businesses.masters', 'businesses.clients']);
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

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
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
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
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
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
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
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
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

        $business->update($request->validated());

        return redirect()->route('settings.index')->with('success', 'Данные бизнеса обновлены');
    }
}
