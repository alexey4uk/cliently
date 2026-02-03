<?php

namespace App\Http\Requests;

use App\Services\BusinessRolePermissionService;
use App\Traits\HasCurrentBusiness;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    use HasCurrentBusiness;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $client = $this->route('client');
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return false;
        }

        $role = $this->getCurrentBusinessRole();
        if (! $role) {
            return false;
        }

        $service = app(BusinessRolePermissionService::class);

        if ($client) {
            return $service->hasPermission($role->id, 'client.clients.update');
        }

        return $service->hasPermission($role->id, 'client.clients.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $client = $this->route('client');
        $business = $this->getCurrentBusiness();
        $phoneRule = ['required', 'string', 'regex:/^\+[0-9]{10,15}$/'];
        if ($business) {
            $phoneRule[] = Rule::unique('clients', 'phone')->where('business_id', $business->id);
            if ($client) {
                $phoneRule[array_key_last($phoneRule)] = Rule::unique('clients', 'phone')->where('business_id', $business->id)->ignore($client->id);
            }
        }

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_country_id' => ['required', 'exists:countries,id'], // в форме — id из справочника, в БД сохраняем код
            'phone' => $phoneRule,
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Имя клиента обязательно для заполнения.',
            'first_name.max' => 'Имя не должно превышать 255 символов.',
            'last_name.max' => 'Фамилия не должна превышать 255 символов.',
            'email.email' => 'Введите корректный email адрес.',
            'email.max' => 'Email не должен превышать 255 символов.',
            'phone_country_id.required' => 'Выберите страну.',
            'phone_country_id.exists' => 'Выбранная страна не найдена.',
            'phone.required' => 'Телефон обязателен для заполнения.',
            'phone.regex' => 'Телефон должен быть в формате E.164 (например, +375291234567).',
        ];
    }
}
