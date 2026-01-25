<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketSettingsRequest;
use App\Models\TicketSettings;

class TicketSettingsController extends Controller
{
    /**
     * Display the ticket settings page.
     */
    public function index()
    {
        $settings = TicketSettings::getGlobal();

        return view('settings.tickets.index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update the ticket settings.
     */
    public function update(TicketSettingsRequest $request)
    {
        $validated = $request->validated();
        $settings = TicketSettings::getGlobal();

        // Обработка чекбоксов
        // После prepareForValidation значения уже преобразованы в boolean
        $enabled = $validated['enabled'] ?? false;
        $emailNotificationsEnabled = $validated['email_notifications_enabled'] ?? false;

        // Обработка массива email получателей
        $emailNotificationRecipients = $validated['email_notification_recipients'] ?? [];

        // Фильтруем пустые значения из массива
        $emailNotificationRecipients = array_filter($emailNotificationRecipients, function ($email) {
            return ! empty(trim($email ?? ''));
        });

        // Подготавливаем данные для обновления
        $updateData = [
            'enabled' => (bool) $enabled,
            'email_notifications_enabled' => (bool) $emailNotificationsEnabled,
            'email_notification_recipients' => array_values($emailNotificationRecipients), // Переиндексируем массив
        ];

        // Обновляем SLA время ответа только если оно передано
        if (isset($validated['sla_response_time']) && $validated['sla_response_time'] !== null) {
            $updateData['sla_response_time'] = (int) $validated['sla_response_time'];
        }

        $settings->update($updateData);

        return redirect()->route('panel.tickets.settings')
            ->with('success', 'Настройки тикет-системы успешно обновлены.');
    }
}
