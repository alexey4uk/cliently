<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketSettingsRequest extends FormRequest
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
            'enabled' => ['nullable', 'boolean'],
            'sla_response_time' => ['nullable', 'integer', 'min:0'],
            'email_notifications_enabled' => ['nullable', 'boolean'],
            'email_notification_recipients' => ['nullable', 'array'],
            'email_notification_recipients.*' => ['email'],
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
            'enabled.boolean' => 'Поле "Включено" должно быть булевым значением.',
            'sla_response_time.integer' => 'Время ответа должно быть числом.',
            'sla_response_time.min' => 'Время ответа не может быть отрицательным.',
            'email_notifications_enabled.boolean' => 'Поле "Email-уведомления" должно быть булевым значением.',
            'email_notification_recipients.array' => 'Получатели уведомлений должны быть массивом.',
            'email_notification_recipients.*.email' => 'Каждый получатель должен быть валидным email адресом.',
        ];
    }
}
