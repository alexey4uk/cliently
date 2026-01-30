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
            'email_notification_recipients.*' => ['nullable', 'email'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Преобразуем чекбоксы: если они не переданы, устанавливаем false
        // Если переданы как '1', преобразуем в true
        $this->merge([
            'enabled' => $this->has('enabled') ? filter_var($this->input('enabled'), FILTER_VALIDATE_BOOLEAN) : false,
            'email_notifications_enabled' => $this->has('email_notifications_enabled') ? filter_var($this->input('email_notifications_enabled'), FILTER_VALIDATE_BOOLEAN) : false,
        ]);

        // Преобразуем пустые строки в null для email получателей
        if ($this->has('email_notification_recipients') && is_array($this->input('email_notification_recipients'))) {
            $recipients = array_map(function ($email) {
                return empty(trim($email ?? '')) ? null : trim($email);
            }, $this->input('email_notification_recipients'));
            $this->merge(['email_notification_recipients' => $recipients]);
        }
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
