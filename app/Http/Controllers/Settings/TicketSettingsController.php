<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketSettingsRequest;
use App\Models\Business;
use App\Models\TicketSettings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketSettingsController extends Controller
{
    /**
     * Display the ticket settings page.
     */
    public function index()
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $settings = TicketSettings::getForBusiness($business->id);

        return view('settings.tickets.index', [
            'business' => $business,
            'settings' => $settings,
        ]);
    }

    /**
     * Update the ticket settings.
     */
    public function update(TicketSettingsRequest $request)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return redirect()->route('welcome')
                ->with('info', 'Сначала создайте бизнес или примите приглашение.');
        }

        $validated = $request->validated();
        $settings = TicketSettings::getForBusiness($business->id);

        // Обработка чекбоксов (если не переданы, значит false)
        $enabled = isset($validated['enabled']) && $validated['enabled'] == '1';
        $emailNotificationsEnabled = isset($validated['email_notifications_enabled']) && $validated['email_notifications_enabled'] == '1';

        // Обработка массивов - если не переданы, используем пустой массив
        $emailNotificationRecipients = $validated['email_notification_recipients'] ?? [];
        
        // Фильтруем пустые значения из массивов
        $emailNotificationRecipients = array_filter($emailNotificationRecipients);

        $settings->update([
            'enabled' => $enabled,
            'sla_response_time' => $validated['sla_response_time'] ?? $settings->sla_response_time,
            'email_notifications_enabled' => $emailNotificationsEnabled,
            'email_notification_recipients' => array_values($emailNotificationRecipients), // Переиндексируем массив
        ]);

        return redirect()->route('panel.tickets.settings')
            ->with('success', 'Настройки тикет-системы успешно обновлены.');
    }
}
