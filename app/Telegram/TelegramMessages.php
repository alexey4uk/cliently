<?php

namespace App\Telegram;

/**
 * Хранилище всех текстовых сообщений для Telegram бота
 * Позволяет централизованно управлять текстами и переводами
 */
class TelegramMessages
{
    // ==================== ОБЩИЕ СООБЩЕНИЯ ====================
    public const MSG_CANCEL = 'Отменено.';

    public const MSG_ERROR = '❌ Ошибка при создании записи. Попробуйте снова.';

    public const MSG_NOT_FOUND = 'Данные не найдены.';

    public const MSG_SESSION_EXPIRED = 'Сессия истекла. Начните запись заново.';

    public const MSG_UNKNOWN_COMMAND = 'Неизвестная команда.';

    public const MSG_NO_SLOTS = 'Нет свободного времени.';

    public const MSG_NO_LOCATIONS = 'Нет доступных локаций.';

    public const MSG_NO_SERVICES = 'Нет доступных услуг.';

    public const MSG_NO_MASTERS = 'Нет доступных мастеров.';

    public const MSG_EMPTY_NAME = 'Имя не может быть пустым:';

    public const MSG_SHORT_NAME = 'Имя слишком короткое:';

    public const MSG_USE_BUTTONS = 'Используйте кнопки для выбора.';

    // ==================== ВЫБОР ДАННЫХ (один эмодзи на шаг для навигации) ====================
    public const MSG_SELECT_LOCATION = '📍 Выберите локацию:';

    public const MSG_SELECT_SERVICE = '✂️ Выберите услугу:';

    public const MSG_SELECT_MASTER = '👤 Выберите мастера:';

    public const MSG_SELECT_DATE = '📅 Выберите дату для {master}:';

    public const MSG_SELECT_DATE_ANY_MASTER = '📅 Выберите дату:';

    public const MSG_ANY_MASTER = 'Любой мастер';

    public const MSG_SELECT_TIME = '🕐 Выберите время на <b>{date}</b>:';

    public const MSG_SELECT_CONFIRM = 'Подтвердите запись:';

    // ==================== ВАЛИДАЦИЯ (без эмодзи — подсказка, не критичная ошибка) ====================
    public const MSG_PHONE_INVALID = "Неверный формат\n\nПравильно: +375291234567\nИли: 375291234567\n\nВведите номер:";

    public const MSG_NOTES_TOO_LONG = "Слишком длинно (макс. 200 символов)\n\nСократите:";

    // ==================== ВВОД ДАННЫХ ====================
    public const MSG_ENTER_NAME = 'Введите ваше имя:';

    public const MSG_ENTER_PHONE = "Введите телефон:\nПример: +375291234567";

    public const MSG_ENTER_NOTES = "Примечание (необязательно):\nАллергия, предпочтения и т.д.\n\nИли нажмите Пропустить";

    // ==================== ПОДТВЕРЖДЕНИЕ ====================
    public const MSG_CONFIRMATION_HEADER = "📋 ПОДТВЕРЖДЕНИЕ\n\n";

    public const MSG_CONFIRMATION_LINE = '{label}: {value}';

    public const MSG_APPOINTMENT_CREATED = "✅ Запись создана!\n\nДата: {date}\nВремя: {time}\nУслуга: {service}\nМастер: {master}\nЛокация: {location}\n\nСвяжемся для подтверждения.";

    /** Вопрос после создания записи: подтвердить сразу или отменить */
    public const MSG_CONFIRM_APPOINTMENT_QUESTION = 'Подтверждаете запись?';

    public const MSG_APPOINTMENT_CONFIRMED_BY_YOU = '✅ Запись подтверждена.';

    public const MSG_APPOINTMENT_CANCELLED_BY_YOU = 'Запись отменена.';

    /**
     * Сообщение клиенту при завершении записи (благодарность).
     * Плейсхолдер: {business_name}. Варианты: «Спасибо, что выбрали {business_name}!» или «Благодарим за визит в {business_name}!»
     */
    public const MSG_APPOINTMENT_COMPLETED_FOR_CLIENT = "✅ Запись завершена.\n\nСпасибо, что выбрали {business_name}! Будем рады видеть вас снова.";

    /** Напоминание о предстоящей записи (клиенту). Плейсхолдеры: business_name, date, time, service, master */
    public const MSG_APPOINTMENT_REMINDER = "⏰ Напоминание от {business_name}\n\nВаша запись:\n📅 {date}\n🕐 {time}\n✂️ {service}\n👤 {master}\n\nЖдём вас!";

    /** Приглашение записаться снова. Плейсхолдеры: business_name, booking_url */
    public const MSG_REENGAGEMENT = "👋 {business_name}\n\nСкучаем! Давно не виделись — будем рады снова вас видеть.\n\nЗаписаться: {booking_url}";

    /** Новый клиент (сотрудникам). Краткое сообщение. */
    public const MSG_CLIENT_NEW = "👤 Новый клиент записался\n\n{client_name}\nУслуга: {service}\nДата: {date}, время: {time}";

    /** Приближающаяся запись (сотрудникам). */
    public const MSG_APPOINTMENT_UPCOMING = "⏰ Приближается запись\n\n{client_name}\nУслуга: {service}\nДата: {date}, время: {time}";

    // ==================== СТАТУСЫ ВВОДА (краткий итог перед следующим полем) ====================
    public const MSG_STATUS_NAME = '👤 Имя: {name}';

    public const MSG_STATUS_PHONE = '📞 Телефон: {phone}';

    // ==================== КНОПКИ ====================
    public const BTN_BACK = 'Назад';

    public const BTN_CANCEL = 'Отмена';

    public const BTN_SKIP = 'Пропустить';

    public const BTN_CONFIRM = 'Подтвердить';

    public const BTN_RESTART = 'Начать заново';

    // ==================== РЕДАКТИРОВАНИЕ ====================
    public const MSG_EDIT_NAME = 'Имя:';

    public const MSG_EDIT_PHONE = 'Телефон:';

    public const MSG_EDIT_NOTES = 'Примечание:';

    // ==================== СТАРТ ====================
    public const MSG_START = '👋 Здравствуйте. Воспользуйтесь меню или введите /help для списка доступных команд.';

    public const MSG_BUSINESS_NOT_FOUND = 'Бизнес не найден.';

    public const MSG_ACCOUNT_CONNECTED = '✅ Аккаунт подключен. Вы будете получать уведомления.';

    public const MSG_NO_BUSINESSES = 'Нет доступных бизнесов.';

    public const MSG_SELECT_BUSINESS_CATALOG = '🏢 Выберите бизнес для записи:';

    // ==================== ПОИСК ====================
    public const MSG_SEARCH_PROMPT = 'Введите название бизнеса для поиска:';

    public const MSG_SEARCH_RESULTS = 'Результаты поиска по "{query}":';

    public const MSG_SEARCH_NO_RESULTS = 'Бизнесы с таким названием не найдены.';

    public const MSG_SEARCH_TOO_SHORT = 'Введите минимум 2 символа для поиска.';

    public const MSG_PAGE_INFO = 'Страница {current} из {total}';

    public const MSG_NO_PREV_PAGE = 'Нет предыдущей страницы.';

    public const MSG_NO_NEXT_PAGE = 'Нет следующей страницы.';

    // ==================== КРИТИЧЕСКИЕ ОШИБКИ (с ❌ — только реальные сбои/ограничения) ====================
    public const MSG_BOOKING_DISABLED = '❌ Запись через Telegram бота недоступна для этого бизнеса. Пожалуйста, используйте веб-форму для записи.';

    public const MSG_ONLINE_BOOKING_DISABLED = '❌ Онлайн-запись временно недоступна. Пожалуйста, свяжитесь с нами напрямую для записи.';

    public const MSG_DATA_ERROR = '❌ Ошибка данных. Попробуйте снова.';

    public const MSG_SESSION_NOT_FOUND = '❌ Сессия не найдена.';

    public const MSG_REQUEST_ERROR = '❌ Ошибка при обработке запроса. Пожалуйста, попробуйте позже.';

    public const MSG_MONTHLY_LIMIT = '❌ Достигнут месячный лимит записей. Пожалуйста, свяжитесь с нами напрямую для записи.';

    public const MSG_CLIENT_LIMIT = '❌ Достигнут лимит клиентов. Пожалуйста, свяжитесь с нами напрямую для записи.';

    /**
     * Форматирует сообщение с подстановкой значений
     */
    public static function format(string $template, array $data): string
    {
        $result = $template;
        foreach ($data as $key => $value) {
            $result = str_replace('{'.$key.'}', $value, $result);
        }

        return $result;
    }
}
