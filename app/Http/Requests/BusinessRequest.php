<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusinessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Для бизнеса проверяем по маршруту
        // Если маршрут /settings/business (без параметра), то это update
        // Если маршрут /settings/business/create, то это create
        $business = $this->route('business');
        
        if ($business) {
            return $this->user()->can('businesses.update');
        }
        
        // Проверяем, это создание или редактирование по URL
        $routeName = $this->route()->getName();
        if (str_contains($routeName, 'create') || $this->isMethod('post')) {
            return $this->user()->can('businesses.create');
        }
        
        return $this->user()->can('businesses.update');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Получаем бизнес из route или из сессии/пользователя
        $business = $this->route('business');
        $businessId = $business?->id ?? null;

        // Если бизнес не в route, пытаемся получить из пользователя
        if (! $businessId && auth()->check()) {
            $userBusiness = auth()->user()->businesses->first();
            $businessId = $userBusiness?->id;
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('businesses', 'slug')->ignore($businessId),
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],
            'description' => ['nullable', 'string'],
            'phone' => [
                'required',
                'string',
                'max:12',
                'digits:12',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Поле "Название бизнеса" обязательно для заполнения.',
            'slug.required' => 'Поле "URL-адрес" обязательно для заполнения.',
            'slug.unique' => 'Этот URL-адрес уже занят.',
            'slug.regex' => 'URL-адрес может содержать только латинские буквы, цифры и дефисы.',
            'phone.required' => 'Поле "Телефон" обязательно для заполнения.',
            'phone.regex' => 'Телефон должен быть в формате +375XXXXXXXXX (9 цифр после +375).',
        ];
    }
}
