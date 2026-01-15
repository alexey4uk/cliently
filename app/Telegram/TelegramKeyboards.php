<?php

namespace App\Telegram;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use App\Telegram\TelegramMessages;

/**
 * Фабрика клавиатур для Telegram бота
 * Создает часто используемые раскладки кнопок
 */
class TelegramKeyboards
{
    // ==================== БАЗОВЫЕ КЛАВИАТУРЫ ====================
    
    /**
     * Только кнопка "Отмена"
     */
    public static function cancelOnly(): Keyboard
    {
        return Keyboard::make()->row([
            Button::make(TelegramMessages::BTN_CANCEL)->action('cancel'),
        ]);
    }

    /**
     * Кнопки "Начать заново" и "Отмена" в одной строке
     */
    public static function restartAndCancel(): Keyboard
    {
        return Keyboard::make()->row([
            Button::make('🔄 Начать заново')->action('restart'),
            Button::make(TelegramMessages::BTN_CANCEL)->action('cancel'),
        ]);
    }

    /**
     * Кнопки "Пропустить" и "Отмена"
     */
    public static function skipAndCancel(): Keyboard
    {
        return Keyboard::make()->row([
            Button::make(TelegramMessages::BTN_SKIP)->action('skip_notes'),
            Button::make(TelegramMessages::BTN_CANCEL)->action('cancel'),
        ]);
    }

    // ==================== ВЫБОР ДАННЫХ ====================
    
    /**
     * Сетка выбора с навигацией
     * 
     * @param Button[] $buttons Массив кнопок выбора
     * @param int $chunkSize Количество кнопок в строке
     */
    public static function selectionGrid(array $buttons, int $chunkSize): Keyboard
    {
        return Keyboard::make()
            ->row($buttons)
            ->chunk($chunkSize)
            ->row([
                Button::make('🔄 Начать заново')->action('restart'),
                Button::make(TelegramMessages::BTN_CANCEL)->action('cancel'),
            ]);
    }

    /**
     * Клавиатура для выбора локаций
     * @param \Illuminate\Database\Eloquent\Collection|\App\Models\Location[] $locations
     */
    public static function locations($locations): Keyboard
    {
        $buttons = [];
        foreach ($locations as $location) {
            $buttons[] = Button::make($location->name)->action("location_{$location->id}");
        }
        // Локация - первый шаг, кнопка "Назад" не нужна
        return Keyboard::make()
            ->row($buttons)
            ->chunk(1)
            ->row([
                Button::make(TelegramMessages::BTN_CANCEL)->action('cancel'),
            ]);
    }

    /**
     * Клавиатура для выбора услуг
     * @param \Illuminate\Database\Eloquent\Collection|\App\Models\Service[] $services
     */
    public static function services($services): Keyboard
    {
        $buttons = [];
        foreach ($services as $service) {
            $buttons[] = Button::make("{$service->name} ({$service->duration} мин)")->action("service_{$service->id}");
        }
        return self::selectionGrid($buttons, 1);
    }

    /**
     * Клавиатура для выбора мастеров
     * @param \Illuminate\Database\Eloquent\Collection|\App\Models\Master[] $masters
     */
    public static function masters($masters): Keyboard
    {
        $buttons = [];
        foreach ($masters as $master) {
            $buttons[] = Button::make($master->first_name . ' ' . $master->last_name)->action("master_{$master->id}");
        }
        return self::selectionGrid($buttons, 1);
    }

    /**
     * Клавиатура для выбора дат (календарь как в веб-версии)
     * @param string $month Год и месяц в формате 'Y-m'
     * @param array $availableDates Массив доступных дат (формат 'Y-m-d')
     * @param \Carbon\Carbon|null $selectedDate Выбранная дата
     * @param bool $hasPrevMonth Можно ли перейти к предыдущему месяцу
     */
    public static function calendar(string $month, array $availableDates = [], ?\Carbon\Carbon $selectedDate = null, bool $hasPrevMonth = true): Keyboard
    {
        $keyboard = Keyboard::make();
        
        // Заголовок календаря - дни недели (как недоступные кнопки)
        $weekDays = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
        $weekDayButtons = [];
        foreach ($weekDays as $day) {
            $weekDayButtons[] = Button::make($day)->action('disabled_weekday');
        }
        $keyboard = $keyboard->row($weekDayButtons);
        
        // Генерируем календарь
        $startDate = \Carbon\Carbon::parse($month . '-01');
        $endDate = $startDate->copy()->endOfMonth();
        
        // Определяем первый день недели (понедельник = 1, воскресенье = 0)
        $firstDayOfWeek = $startDate->dayOfWeek; // 1 = Monday, 7 = Sunday
        $daysToSubtract = $firstDayOfWeek === 1 ? 0 : ($firstDayOfWeek === 0 ? 6 : $firstDayOfWeek - 1);
        
        // Начинаем с понедельника
        $currentDate = $startDate->copy()->subDays($daysToSubtract);
        $today = \Carbon\Carbon::today();
        
        // Генерируем 6 недель (42 дня)
        $weekButtons = [];
        for ($i = 0; $i < 42; $i++) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayNum = $currentDate->day;
            $isCurrentMonth = $currentDate->month === $startDate->month;
            $isPast = $currentDate->lt($today);
            $isToday = $currentDate->isSameDay($today);
            $isSelected = $selectedDate && $currentDate->isSameDay($selectedDate);
            $isAvailable = in_array($dateStr, $availableDates);
            
            // Определяем callback action
            // Только даты текущего месяца, не прошедшие и с доступными слотами - кликабельны
            if ($isCurrentMonth && !$isPast && $isAvailable) {
                $action = "date_{$dateStr}";
                // Формируем текст кнопки для кликабельной даты
                $displayText = (string)$dayNum;
                
                // Добавляем оформление
                if ($isSelected) {
                    $displayText = '✅ ' . $displayText;
                } elseif ($isToday) {
                    $displayText = '•' . $displayText . '•';
                }
            } else {
                $action = "disabled_{$dateStr}"; // Пустая ячейка
                $displayText = ' . '; // Пустая ячейка с точкой для визуального разделения
            }
            
            $weekButtons[] = Button::make($displayText)->action($action);
            
            // Каждые 7 дней - новая строка
            if (count($weekButtons) === 7) {
                $keyboard = $keyboard->row($weekButtons);
                $weekButtons = [];
            }
            
            $currentDate->addDay();
        }
        
        // Добавляем навигацию по месяцам (3 кнопки в ряд)
        $navButtons = [];
        
        if ($hasPrevMonth) {
            $navButtons[] = Button::make('⬅️')->action("calendar_prev_{$month}");
        }
        
        $navButtons[] = Button::make('➡️')->action("calendar_next_{$month}");
        
        // Если нет предыдущего месяца, добавляем пустую кнопку для выравнивания
        if (!$hasPrevMonth) {
            $navButtons = [Button::make('➖')->action('disabled_empty'), ...$navButtons];
        }
        
        $keyboard = $keyboard->row($navButtons);
        
        // Кнопки навигации
        return $keyboard
            ->row([
                Button::make('🔄 Начать заново')->action('restart'),
                Button::make(TelegramMessages::BTN_CANCEL)->action('cancel'),
            ]);
    }
    
    /**
     * Клавиатура для выбора дат (простой вариант - 6 дней вперед)
     */
    public static function datesSimple(array $dates): Keyboard
    {
        $buttons = [];
        foreach ($dates as $date) {
            $buttons[] = Button::make($date['display'])->action("date_{$date['value']}");
        }
        return self::selectionGrid($buttons, 3);
    }

    /**
     * Клавиатура для выбора времени
     */
    public static function times(array $times): Keyboard
    {
        $buttons = [];
        foreach ($times as $time) {
            if (str_contains($time, ':')) {
                $display = $time;
                $callback = $time;
            } else {
                $display = $time . ':00';
                $callback = $time;
            }
            $buttons[] = Button::make($display)->action("time_{$callback}");
        }
        return self::selectionGrid($buttons, 3);
    }


    /**
     * Получает доступные даты для месяца
     * @param \App\Services\AppointmentSlotService $slotService
     * @param int $serviceId
     * @param int $masterId
     * @param int $locationId
     * @param string $month Год и месяц в формате 'Y-m'
     * @return array Массив доступных дат (формат 'Y-m-d')
     */
    public static function getAvailableDatesForMonth($slotService, int $serviceId, int $masterId, int $locationId, string $month): array
    {
        $availableDates = [];
        $startDate = \Carbon\Carbon::parse($month . '-01');
        $endDate = $startDate->copy()->endOfMonth();
        $today = \Carbon\Carbon::today();
        
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            // Пропускаем прошедшие даты
            if ($current->lt($today)) {
                $current->addDay();
                continue;
            }
            
            // Проверяем наличие слотов
            $debugInfo = [];
            $availableSlots = $slotService->getAvailableSlots(
                $serviceId,
                $current->format('Y-m-d'),
                $masterId,
                $locationId,
                $debugInfo
            );
            
            if (!empty($availableSlots)) {
                $availableDates[] = $current->format('Y-m-d');
            }
            
            $current->addDay();
        }
        
        return $availableDates;
    }

    /**
     * Проверяет, есть ли предыдущий месяц (не раньше сегодня)
     * @param string $month Год и месяц в формате 'Y-m'
     * @return bool
     */
    public static function hasPrevMonth(string $month): bool
    {
        $monthDate = \Carbon\Carbon::parse($month . '-01');
        $today = \Carbon\Carbon::today();
        return $monthDate->gt($today->startOfMonth());
    }

    /**
     * Клавиатура для выбора времени (нет слотов)
     */
    public static function timesEmpty(): Keyboard
    {
        return Keyboard::make()->row([
            Button::make('🔄 Начать заново')->action('restart'),
            Button::make(TelegramMessages::BTN_CANCEL)->action('cancel'),
        ]);
    }

    // ==================== ПОДТВЕРЖДЕНИЕ ====================
    
    /**
     * Клавиатура подтверждения записи
     */
    public static function confirmation(): Keyboard
    {
        return Keyboard::make()
            ->row([
                Button::make(TelegramMessages::BTN_CONFIRM)->action('confirm_appointment'),
                Button::make(TelegramMessages::BTN_CANCEL)->action('cancel'),
            ])
            ->row([
                Button::make('🔄 Начать заново')->action('restart'),
            ]);
    }

    // ==================== КАТАЛОГ БИЗНЕСОВ ====================
    
    /**
     * Клавиатура для каталога бизнесов
     * @param \Illuminate\Database\Eloquent\Collection|\App\Models\Business[] $businesses
     */
    public static function businessCatalog($businesses): Keyboard
    {
        $keyboard = Keyboard::make();
        
        foreach ($businesses as $business) {
            $keyboard = $keyboard->row([
                Button::make("🏢 {$business->name}")->action("business_{$business->id}"),
            ]);
        }
        
        return $keyboard;
    }

}
