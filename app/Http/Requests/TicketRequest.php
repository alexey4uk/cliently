<?php

namespace App\Http\Requests;

use App\Services\BusinessRolePermissionService;
use App\Traits\HasCurrentBusiness;
use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
{
    use HasCurrentBusiness;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $business = $this->getCurrentBusiness();
        
        if (!$business) {
            return false;
        }
        
        $role = $this->getCurrentBusinessRole();
        if (!$role) {
            return false;
        }
        
        $service = app(BusinessRolePermissionService::class);
        
        // Для тикетов используется {id} вместо {ticket} в маршрутах
        // Проверяем по методу запроса
        if ($this->isMethod('post')) {
            return $service->hasPermission($role->id, 'client.tickets.create');
        }
        
        // Для PATCH/PUT это обновление
        return $service->hasPermission($role->id, 'client.tickets.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category_id' => ['nullable', 'exists:ticket_categories,id'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'attachments.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt'],
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
            'title.required' => 'Тема тикета обязательна для заполнения.',
            'title.max' => 'Тема не должна превышать 255 символов.',
            'description.required' => 'Описание тикета обязательно для заполнения.',
            'category_id.exists' => 'Выбранная категория не существует.',
            'client_id.exists' => 'Выбранный клиент не существует.',
            'attachments.*.file' => 'Файл должен быть загружен.',
            'attachments.*.max' => 'Размер файла не должен превышать 10 МБ.',
            'attachments.*.mimes' => 'Разрешенные форматы файлов: jpg, jpeg, png, gif, pdf, doc, docx, txt.',
        ];
    }
}
