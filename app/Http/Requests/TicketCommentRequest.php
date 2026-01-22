<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketCommentRequest extends FormRequest
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
            'content' => ['required', 'string'],
            'is_internal' => ['nullable', 'boolean'],
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
            'content.required' => 'Содержимое комментария обязательно для заполнения.',
            'is_internal.boolean' => 'Поле "Внутренний комментарий" должно быть булевым значением.',
            'attachments.*.file' => 'Файл должен быть загружен.',
            'attachments.*.max' => 'Размер файла не должен превышать 10 МБ.',
            'attachments.*.mimes' => 'Разрешенные форматы файлов: jpg, jpeg, png, gif, pdf, doc, docx, txt.',
        ];
    }
}
