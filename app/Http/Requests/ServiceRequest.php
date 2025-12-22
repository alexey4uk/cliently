<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'duration' => ['required', 'integer', 'min:15'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
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
            'name.required' => 'Название услуги обязательно для заполнения.',
            'name.max' => 'Название услуги не должно превышать 255 символов.',
            'description.max' => 'Описание не должно превышать 1000 символов.',
            'duration.required' => 'Длительность обязательна для заполнения.',
            'duration.integer' => 'Длительность должна быть числом.',
            'duration.min' => 'Длительность должна быть не менее 15 минут.',
            'price.required' => 'Цена обязательна для заполнения.',
            'price.numeric' => 'Цена должна быть числом.',
            'price.min' => 'Цена не может быть отрицательной.',
        ];
    }
}

