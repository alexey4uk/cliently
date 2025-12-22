<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppointmentRequest extends FormRequest
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
            'client_id' => ['required', 'exists:clients,id'],
            'service_id' => ['required', 'exists:services,id'],
            'master_id' => ['nullable', 'exists:masters,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'status' => ['nullable', Rule::in(['pending', 'confirmed', 'completed', 'cancelled'])],
            'notes' => ['nullable', 'string', 'max:1000'],
            'duration' => ['nullable', 'integer', 'min:15'],
            'price' => ['nullable', 'numeric', 'min:0'],
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
            'client_id.required' => 'Клиент обязателен для заполнения.',
            'client_id.exists' => 'Выбранный клиент не существует.',
            'service_id.required' => 'Услуга обязательна для заполнения.',
            'service_id.exists' => 'Выбранная услуга не существует.',
            'master_id.exists' => 'Выбранный мастер не существует.',
            'location_id.exists' => 'Выбранная локация не существует.',
            'date.required' => 'Дата записи обязательна для заполнения.',
            'date.date' => 'Введите корректную дату.',
            'date.after_or_equal' => 'Дата записи не может быть в прошлом.',
            'time.required' => 'Время записи обязательно для заполнения.',
            'time.date_format' => 'Введите корректное время в формате ЧЧ:ММ.',
            'status.in' => 'Выбран некорректный статус записи.',
            'notes.max' => 'Заметки не должны превышать 1000 символов.',
            'duration.integer' => 'Длительность должна быть числом.',
            'duration.min' => 'Длительность должна быть не менее 15 минут.',
            'price.numeric' => 'Цена должна быть числом.',
            'price.min' => 'Цена не может быть отрицательной.',
        ];
    }
}
