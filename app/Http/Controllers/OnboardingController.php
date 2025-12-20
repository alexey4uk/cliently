<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Location;
use App\Models\Master;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class OnboardingController extends Controller
{
    public function business()
    {
        if (Auth::user()->businesses->isNotEmpty()) {
            return redirect()->route('onboarding.location');
        }

        return view('onboarding.step1-business');
    }

    /**
     * @throws Throwable
     */
    public function storeBusiness(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:businesses,slug|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'description' => 'nullable|string',
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^\+375\d{9}$/',
            ],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
        ], [
            'phone.required' => 'Поле "Телефон" обязательно для заполнения.',
            'phone.regex' => 'Телефон должен быть в формате +375XXXXXXXXX (9 цифр после +375).',
            'first_name.required' => 'Поле "Имя" обязательно для заполнения.',
            'first_name.max' => 'Поле "Имя" не может быть длиннее 255 символов.',
            'last_name.required' => 'Поле "Фамилия" обязательно для заполнения.',
            'last_name.max' => 'Поле "Фамилия" не может быть длиннее 255 символов.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $business = Business::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'phone' => $validated['phone'],
            ]);

            $business->users()->attach($request->user(), [
                'role' => 'owner',
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
            ]);

            session(['onboarding.business_id' => $business->id]);
        });

        return redirect()->route('onboarding.location')->with('info', 'Бизнес добавлен');
    }

    public function location(Request $request)
    {
        $user = $request->user()->load(['businesses.locations']);
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

        if ($business->locations->isNotEmpty()) {
            return redirect()->route('onboarding.service');
        }

        return view('onboarding.step2-location');

    }

    public function storeLocation(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'street' => 'required|string|max:255',
            'house' => 'required|string|max:20',
            'building' => 'nullable|string|max:20',
            'apartment' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^\+375\d{9}$/',
            ],
            'working_hours' => 'required|array',
            'working_hours.from' => 'required_without:working_hours.24_hours|date_format:H:i',
            'working_hours.to' => 'required_without:working_hours.24_hours|date_format:H:i',
            'working_hours.24_hours' => 'nullable|boolean',
            'working_hours.days_off' => 'nullable|array',
            'working_hours.days_off.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ], [
            'name.required' => 'Поле "Название локации" обязательно для заполнения.',
            'city.required' => 'Поле "Город" обязательно для заполнения.',
            'city.max' => 'Поле "Город" не может быть длиннее 100 символов.',
            'street.required' => 'Поле "Улица" обязательно для заполнения.',
            'street.max' => 'Поле "Улица" не может быть длиннее 255 символов.',
            'house.required' => 'Поле "Дом" обязательно для заполнения.',
            'house.max' => 'Поле "Дом" не может быть длиннее 20 символов.',
            'building.max' => 'Поле "Корпус" не может быть длиннее 20 символов.',
            'apartment.max' => 'Поле "Квартира/Офис" не может быть длиннее 20 символов.',
            'phone.required' => 'Поле "Телефон" обязательно для заполнения.',
            'phone.regex' => 'Телефон должен быть в формате +375XXXXXXXXX (9 цифр после +375).',
            'working_hours.required' => 'Необходимо указать время работы.',
            'working_hours.from.required_without' => 'Укажите время начала работы или выберите круглосуточный режим.',
            'working_hours.to.required_without' => 'Укажите время окончания работы или выберите круглосуточный режим.',
            'working_hours.from.date_format' => 'Неверный формат времени начала работы.',
            'working_hours.to.date_format' => 'Неверный формат времени окончания работы.',
        ]);

        $businessId = session('onboarding.business_id') ?? $request->user()->businesses()->first()->id;

        // Формируем оптимизированную структуру working_hours
        $daysOff = $validated['working_hours']['days_off'] ?? [];
        $is24Hours = ! empty($validated['working_hours']['24_hours']);

        $workingHours = [
            'from' => $is24Hours ? '00:00' : ($validated['working_hours']['from'] ?? null),
            'to' => $is24Hours ? '00:00' : ($validated['working_hours']['to'] ?? null),
            '24_hours' => $is24Hours,
            'days_off' => $daysOff,
        ];

        $location = Location::create([
            'business_id' => $businessId,
            'name' => $validated['name'],
            'city' => $validated['city'],
            'street' => $validated['street'],
            'house' => $validated['house'],
            'building' => $validated['building'] ?? null,
            'apartment' => $validated['apartment'] ?? null,
            'description' => $validated['description'] ?? null,
            'phone' => $validated['phone'],
            'working_hours' => json_encode($workingHours),
        ]);

        return redirect()->route('onboarding.service')->with('info', 'Локация добавлена');
    }

    public function service()
    {
        $user = Auth::user()->load(['businesses.locations', 'businesses.services']);

        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

        if ($business->locations->isEmpty()) {
            return redirect()->route('onboarding.location');
        }

        if ($business->services->isNotEmpty()) {
            return redirect()->route('onboarding.master');
        }

        return view('onboarding.step3-service');
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:15|max:480',
            'price' => 'required|numeric|min:0|max:999999',
        ], [
            'name.required' => 'Поле "Название услуги" обязательно для заполнения.',
            'duration.required' => 'Поле "Длительность" обязательно для заполнения.',
            'duration.min' => 'Минимальная длительность услуги — 15 минут.',
            'duration.max' => 'Максимальная длительность услуги — 480 минут (8 часов).',
            'price.required' => 'Поле "Цена" обязательно для заполнения.',
            'price.min' => 'Цена не может быть отрицательной.',
            'price.max' => 'Цена не может превышать 999 999.',
        ]);

        $businessId = $request->user()->businesses()->first()->id;

        $service = Service::create([
            'business_id' => $businessId,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'duration' => $validated['duration'],
            'price' => $validated['price'],
            'is_active' => true,
        ]);

        return redirect()->route('onboarding.master')->with('info', 'Услуга добавлена');
    }

    public function master()
    {
        $user = Auth::user()->load([
            'businesses.locations',
            'businesses.services',
            'businesses.masters',
        ]);

        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

        if ($business->locations->isEmpty()) {
            return redirect()->route('onboarding.location');
        }

        if ($business->services->isEmpty()) {
            return redirect()->route('onboarding.service');
        }

        if ($business->masters->isNotEmpty()) {
            return redirect()->route('onboarding.complete');
        }

        return view('onboarding.step4-master');
    }

    public function storeMaster(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'specialization' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^\+375\d{9}$/',
            ],
            'email' => 'nullable|email|max:255',
            'working_hours' => 'required|array',
            'working_hours.from' => 'required_without:working_hours.24_hours|date_format:H:i',
            'working_hours.to' => 'required_without:working_hours.24_hours|date_format:H:i',
            'working_hours.24_hours' => 'nullable|boolean',
            'working_hours.days_off' => 'nullable|array',
            'working_hours.days_off.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ], [
            'first_name.required' => 'Поле "Имя" обязательно для заполнения.',
            'first_name.max' => 'Поле "Имя" не может быть длиннее 255 символов.',
            'last_name.max' => 'Поле "Фамилия" не может быть длиннее 255 символов.',
            'specialization.required' => 'Поле "Специализация" обязательно для заполнения.',
            'phone.required' => 'Поле "Телефон" обязательно для заполнения.',
            'phone.regex' => 'Телефон должен быть в формате +375XXXXXXXXX (9 цифр после +375).',
            'email.email' => 'Неверный формат email адреса.',
            'working_hours.required' => 'Необходимо указать время работы.',
            'working_hours.from.required_without' => 'Укажите время начала работы или выберите круглосуточный режим.',
            'working_hours.to.required_without' => 'Укажите время окончания работы или выберите круглосуточный режим.',
            'working_hours.from.date_format' => 'Неверный формат времени начала работы.',
            'working_hours.to.date_format' => 'Неверный формат времени окончания работы.',
            'working_hours.days_off.in' => 'Выбран неверный день недели.',
        ]);

        $business = $request->user()->businesses()->with(['locations', 'services'])->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

        // Формируем оптимизированную структуру working_hours
        $daysOff = $validated['working_hours']['days_off'] ?? [];
        $is24Hours = ! empty($validated['working_hours']['24_hours']);

        $workingHours = [
            'from' => $is24Hours ? '00:00' : ($validated['working_hours']['from'] ?? null),
            'to' => $is24Hours ? '00:00' : ($validated['working_hours']['to'] ?? null),
            '24_hours' => $is24Hours,
            'days_off' => $daysOff,
        ];

        $master = Master::create([
            'business_id' => $business->id,
            'user_id' => $request->user()->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'description' => $validated['description'] ?? null,
            'specialization' => $validated['specialization'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'working_hours' => json_encode($workingHours),
        ]);

        $locationId = $business->locations->first()?->id;
        $serviceId = $business->services->first()?->id;

        if ($locationId) {
            $master->locations()->attach($locationId);
        }

        if ($serviceId) {
            $master->services()->attach($serviceId);
        }

        return redirect()->route('onboarding.complete');
    }

    public function complete()
    {
        $user = Auth::user()->load(['businesses.locations', 'businesses.services', 'businesses.masters']);
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

        return view('onboarding.complete', [
            'business' => $business,
        ]);
    }
}
