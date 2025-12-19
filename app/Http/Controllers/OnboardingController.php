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

        return redirect()->route('onboarding.location');
    }

    public function location(Request $request)
    {
        $user = auth()->user()->load(['businesses.locations']);
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
            'address' => 'required|string|max:500',
            'description' => 'nullable|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'working_hours' => 'required|array',
            'working_hours.from' => 'required_without:working_hours.24_hours|date_format:H:i',
            'working_hours.to' => 'required_without:working_hours.24_hours|date_format:H:i',
            'working_hours.24_hours' => 'nullable|boolean',
        ], [
            'name.required' => 'Поле "Название локации" обязательно для заполнения.',
            'address.required' => 'Поле "Адрес" обязательно для заполнения.',
            'phone.required' => 'Поле "Телефон" обязательно для заполнения.',
            'working_hours.required' => 'Необходимо указать время работы.',
            'working_hours.from.required_without' => 'Укажите время начала работы или выберите круглосуточный режим.',
            'working_hours.to.required_without' => 'Укажите время окончания работы или выберите круглосуточный режим.',
            'working_hours.from.date_format' => 'Неверный формат времени начала работы.',
            'working_hours.to.date_format' => 'Неверный формат времени окончания работы.',
            'email.email' => 'Неверный формат email адреса.',
        ]);

        $businessId = session('onboarding.business_id') ?? $request->user()->businesses()->first()->id;

        // Формируем working_hours для всех дней недели
        $workingHours = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        if (! empty($validated['working_hours']['24_hours'])) {
            // Круглосуточный режим
            foreach ($days as $day) {
                $workingHours[$day] = [
                    'from' => '00:00',
                    'to' => '00:00',
                    'day_off' => false,
                ];
            }
        } else {
            // Обычный режим - одинаковое время для всех дней
            foreach ($days as $day) {
                $workingHours[$day] = [
                    'from' => $validated['working_hours']['from'] ?? null,
                    'to' => $validated['working_hours']['to'] ?? null,
                    'day_off' => false,
                ];
            }
        }

        $location = Location::create([
            'business_id' => $businessId,
            'name' => $validated['name'],
            'address' => $validated['address'],
            'description' => $validated['description'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'working_hours' => json_encode($workingHours),
        ]);

        return redirect()->route('onboarding.service');
    }

    public function service()
    {
        $user = auth()->user()->load(['businesses.locations', 'businesses.services']);

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

        return redirect()->route('onboarding.master');
    }

    public function master()
    {
        $user = auth()->user()->load([
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
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ], [
            'name.required' => 'Поле "Имя мастера" обязательно для заполнения.',
            'specialization.required' => 'Поле "Специализация" обязательно для заполнения.',
            'phone.required' => 'Поле "Телефон" обязательно для заполнения.',
            'email.email' => 'Неверный формат email адреса.',
        ]);

        $business = $request->user()->businesses()->with(['locations', 'services'])->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

        $master = Master::create([
            'business_id' => $business->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'specialization' => $validated['specialization'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
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
        $user = auth()->user()->load(['businesses.locations', 'businesses.services', 'businesses.masters']);
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

        return view('onboarding.complete', [
            'business' => $business,
        ]);
    }
}
