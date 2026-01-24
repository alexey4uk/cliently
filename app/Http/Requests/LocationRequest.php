<?php

namespace App\Http\Requests;

use App\Services\BusinessRolePermissionService;
use App\Traits\HasCurrentBusiness;
use Illuminate\Foundation\Http\FormRequest;

class LocationRequest extends FormRequest
{
    use HasCurrentBusiness;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $location = $this->route('location');
        $business = $this->getCurrentBusiness();
        
        if (!$business) {
            return false;
        }
        
        $role = $this->getCurrentBusinessRole();
        if (!$role) {
            return false;
        }
        
        $service = app(BusinessRolePermissionService::class);
        
        if ($location) {
        return $service->hasPermission($role->id, 'client.locations.update');
        }
        
        return $service->hasPermission($role->id, 'client.locations.create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'street' => ['required', 'string', 'max:255'],
            'house' => ['required', 'string', 'max:20'],
            'building' => ['nullable', 'string', 'max:20'],
            'apartment' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'phone_country_id' => ['required', 'exists:countries,id'],
            'phone' => ['required', 'string', 'regex:/^\+[0-9]{10,15}$/'],
            'working_hours' => ['required', 'array'],
            'working_hours.from' => ['required_without:working_hours.24_hours', 'date_format:H:i'],
            'working_hours.to' => ['required_without:working_hours.24_hours', 'date_format:H:i'],
            'working_hours.24_hours' => ['nullable', 'boolean'],
            'working_hours.days_off' => ['nullable', 'array'],
            'working_hours.days_off.*' => ['string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Поле "Название локации" обязательно для заполнения.',
            'city.required' => 'Поле "Город" обязательно для заполнения.',
            'city.max' => 'Поле "Город" не может быть длиннее 100 символов.',
            'street.required' => 'Поле "Улица" обязательно для заполнения.',
            'street.max' => 'Поле "Улица" не может быть длиннее 255 символов.',
            'house.required' => 'Поле "Дом" обязательно для заполнения.',
            'house.max' => 'Поле "Дом" не может быть длиннее 20 символов.',
            'building.max' => 'Поле "Корпус" не может быть длиннее 20 символов.',
            'apartment.max' => 'Поле "Квартира/Офис" не может быть длиннее 20 символов.',
            'phone_country_id.required' => 'Выберите страну.',
            'phone_country_id.exists' => 'Выбранная страна не найдена.',
            'phone.required' => 'Поле "Телефон" обязательно для заполнения.',
            'phone.regex' => 'Телефон должен быть в формате E.164 (например, +375291234567).',
            'working_hours.required' => 'Необходимо указать время работы.',
            'working_hours.from.required_without' => 'Укажите время начала работы или выберите круглосуточный режим.',
            'working_hours.to.required_without' => 'Укажите время окончания работы или выберите круглосуточный режим.',
            'working_hours.from.date_format' => 'Неверный формат времени начала работы.',
            'working_hours.to.date_format' => 'Неверный формат времени окончания работы.',
        ];
    }
}
