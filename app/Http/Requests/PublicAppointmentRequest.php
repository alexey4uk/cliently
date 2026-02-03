<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use App\Models\Master;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PublicAppointmentRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone_country_code' => ['nullable', 'string', 'size:2'], // ISO из виджета, в БД пишем как есть
            'phone' => ['required', 'string', 'regex:/^\+[0-9]{10,15}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'service_id' => ['required', 'exists:services,id'],
            'master_id' => ['required', 'exists:masters,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:1000'],
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
            'first_name.required' => 'Имя обязательно для заполнения.',
            'phone.required' => 'Телефон обязателен для заполнения.',
            'phone.regex' => 'Телефон должен быть в формате E.164 (например, +375291234567).',
            'service_id.required' => 'Услуга обязательна для заполнения.',
            'service_id.exists' => 'Выбранная услуга не существует.',
            'master_id.required' => 'Мастер обязателен для выбора.',
            'master_id.exists' => 'Выбранный мастер не существует.',
            'location_id.required' => 'Локация обязательна для выбора.',
            'location_id.exists' => 'Выбранная локация не существует.',
            'date.required' => 'Дата записи обязательна для заполнения.',
            'date.date' => 'Введите корректную дату.',
            'date.after_or_equal' => 'Дата записи не может быть в прошлом.',
            'time.required' => 'Время записи обязательно для заполнения.',
            'time.date_format' => 'Введите корректное время в формате ЧЧ:ММ.',
            'email.email' => 'Введите корректный email адрес.',
            'notes.max' => 'Заметки не должны превышать 1000 символов.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $serviceId = $this->input('service_id');
            $masterId = $this->input('master_id');
            $date = $this->input('date');
            $time = $this->input('time');

            // Получаем длительность услуги
            $service = Service::find($serviceId);
            if (! $service) {
                return;
            }

            $appointmentDuration = $service->duration;

            // Мастер обязателен, проверяем его
            $master = Master::find($masterId);
            if (! $master) {
                $validator->errors()->add('master_id', 'Выбранный мастер не найден.');

                return;
            }

            // Проверяем, что мастер предоставляет эту услугу
            if (! $master->services()->where('services.id', $serviceId)->exists()) {
                $validator->errors()->add('master_id', 'Выбранный мастер не предоставляет эту услугу.');

                return;
            }

            // Проверяем рабочее время мастера
            $selectedDate = Carbon::parse($date);
            if ($master->isDayOff($selectedDate)) {
                $validator->errors()->add('date', 'Выбранная дата является выходным днем для мастера.');

                return;
            }

            $workingTime = $master->getWorkingTimeForDate($selectedDate);
            if ($workingTime) {
                $startTime = Carbon::parse($time);
                $endTime = $startTime->copy()->addMinutes($appointmentDuration);
                $workStart = Carbon::parse($workingTime['from']);
                $workEnd = Carbon::parse($workingTime['to']);

                if ($startTime->lt($workStart) || $endTime->gt($workEnd)) {
                    $validator->errors()->add('time', 'Выбранное время выходит за рамки рабочего времени мастера.');

                    return;
                }
            }

            // Проверяем пересечения с существующими записями
            if (Appointment::hasConflictForMaster($masterId, Carbon::parse($date), $time, $appointmentDuration)) {
                $validator->errors()->add('time', 'Выбранное время уже занято. Пожалуйста, выберите другое время.');
            }
        });
    }
}
