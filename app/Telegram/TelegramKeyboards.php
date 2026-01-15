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
     * Кнопки "Назад" и "Отмена" в одной строке
     */
    public static function backAndCancel(string $backAction): Keyboard
    {
        return Keyboard::make()->row([
            Button::make(TelegramMessages::BTN_BACK)->action($backAction),
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
     * @param string $backAction Действие для кнопки "Назад"
     */
    public static function selectionGrid(array $buttons, int $chunkSize, string $backAction): Keyboard
    {
        return Keyboard::make()
            ->row($buttons)
            ->chunk($chunkSize)
            ->row([
                Button::make(TelegramMessages::BTN_BACK)->action($backAction),
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
        return self::selectionGrid($buttons, 2, 'back_to_location');
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
        return self::selectionGrid($buttons, 2, 'back_to_service');
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
        return self::selectionGrid($buttons, 2, 'back_to_master');
    }

    /**
     * Клавиатура для выбора дат
     */
    public static function dates(array $dates): Keyboard
    {
        $buttons = [];
        foreach ($dates as $date) {
            $buttons[] = Button::make($date['display'])->action("date_{$date['value']}");
        }
        return self::selectionGrid($buttons, 3, 'back_to_time');
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
        return self::selectionGrid($buttons, 3, 'back_to_date');
    }

    /**
     * Клавиатура для выбора времени (нет слотов)
     */
    public static function timesEmpty(): Keyboard
    {
        return Keyboard::make()->row([
            Button::make('⬅️ Другая дата')->action('back_to_time'),
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
                Button::make('Имя')->action('edit_name'),
                Button::make('Телефон')->action('edit_phone'),
                Button::make('Примечание')->action('edit_notes'),
            ])
            ->row([
                Button::make(TelegramMessages::BTN_BACK)->action('back_to_time'),
            ]);
    }

    // ==================== РЕДАКТИРОВАНИЕ ====================
    
    /**
     * Клавиатура для редактирования поля
     */
    public static function editField(string $field, string $backAction): Keyboard
    {
        $buttons = [
            Button::make(TelegramMessages::BTN_BACK)->action($backAction),
            Button::make(TelegramMessages::BTN_CANCEL)->action('cancel'),
        ];
        
        if ($field === 'notes') {
            array_unshift($buttons, Button::make(TelegramMessages::BTN_SKIP)->action('skip_notes'));
        }
        
        return Keyboard::make()->row($buttons);
    }

    /**
     * Клавиатура для редактирования имени
     */
    public static function editName(): Keyboard
    {
        return self::editField('name', 'back_to_time');
    }

    /**
     * Клавиатура для редактирования телефона
     */
    public static function editPhone(): Keyboard
    {
        return self::editField('phone', 'back_to_name');
    }

    /**
     * Клавиатура для редактирования заметки
     */
    public static function editNotes(): Keyboard
    {
        return self::editField('notes', 'back_to_time');
    }
}
