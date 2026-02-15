<?php

namespace App\Http\Requests;

use App\Services\BusinessRolePermissionService;
use App\Traits\HasCurrentBusiness;
use Illuminate\Foundation\Http\FormRequest;

class MasterRequest extends FormRequest
{
    use HasCurrentBusiness;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $master = $this->route('master');
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return true;
        }

        $role = $this->getCurrentBusinessRole();
        if (! $role) {
            return false;
        }

        $service = app(BusinessRolePermissionService::class);

        if ($master) {
            return $service->hasPermission($role->id, 'client.masters.update');
        }

        return $service->hasPermission($role->id, 'client.masters.create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'specialization' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'phone_country_id' => ['required', 'exists:countries,id'],
            'phone' => ['required', 'string', 'regex:/^\+[0-9]{10,15}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'location_ids' => ['nullable', 'array'],
            'location_ids.*' => ['exists:locations,id'],
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['exists:services,id'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Поле "Имя" обязательно для заполнения.',
            'first_name.max' => 'Поле "Имя" не может быть длиннее 255 символов.',
            'last_name.max' => 'Поле "Фамилия" не может быть длиннее 255 символов.',
            'specialization.required' => 'Поле "Специализация" обязательно для заполнения.',
            'phone_country_id.required' => 'Выберите страну.',
            'phone_country_id.exists' => 'Выбранная страна не найдена.',
            'phone.required' => 'Поле "Телефон" обязательно для заполнения.',
            'phone.regex' => 'Телефон должен быть в формате E.164 (например, +375291234567).',
            'email.email' => 'Неверный формат email адреса.',
        ];
    }
}
