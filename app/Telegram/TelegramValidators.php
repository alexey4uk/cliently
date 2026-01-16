<?php

namespace App\Telegram;

use App\Telegram\TelegramMessages;

/**
 * Валидаторы данных для Telegram бота
 * Централизованная логика проверки ввода пользователя
 */
class TelegramValidators
{
    /**
     * Валидация имени клиента
     * 
     * @param string $name Введенное имя
     * @return array [bool $isValid, string $resultOrError]
     */
    public static function validateName(string $name): array
    {
        $name = trim($name);
        
        if (empty($name)) {
            return [false, "❌ Имя не может быть пустым. Пожалуйста, введите ваше имя:"];
        }
        
        if (mb_strlen($name) < 2) {
            return [false, "❌ Имя слишком короткое (минимум 2 символа). Пожалуйста, введите корректное имя:"];
        }
        
        // Проверка на наличие цифр или специальных символов
        if (preg_match('/[^а-яА-ЯёЁa-zA-Z\s\-]/u', $name)) {
            return [false, "❌ Имя не должно содержать цифр или специальных символов. Пожалуйста, введите корректное имя:"];
        }
        
        return [true, $name];
    }

    /**
     * Валидация и форматирование телефона
     * 
     * @param string $phone Введенный телефон
     * @return array [bool $isValid, string $formattedPhoneOrError]
     */
    public static function validatePhone(string $phone): array
    {
        $phone = trim($phone);
        
        if (empty($phone)) {
            return [false, TelegramMessages::MSG_PHONE_INVALID];
        }
        
        $cleaned = preg_replace('/[^0-9+]/', '', $phone);
        
        // Автоформатирование
        if (str_starts_with($cleaned, '8')) {
            $cleaned = '+7' . substr($cleaned, 1);
        }
        
        if (!str_starts_with($cleaned, '+')) {
            $cleaned = '+' . $cleaned;
        }
        
        // Проверка формата
        if (!preg_match('/^\+\d{10,15}$/', $cleaned)) {
            return [false, TelegramMessages::MSG_PHONE_INVALID];
        }
        
        return [true, $cleaned];
    }

    /**
     * Проверка, нужно ли пропускать ввод заметки
     * 
     * @param string $notes Введенные заметки
     * @return bool true, если нужно пропустить
     */
    public static function shouldSkipNotes(string $notes): bool
    {
        $notes = trim($notes);
        $skipWords = ['нет', 'нечего', 'нету', 'пропустить', 'skip', '-', 'н'];
        
        return empty($notes) || in_array(mb_strtolower($notes), $skipWords);
    }

    /**
     * Валидация заметки
     * 
     * @param string $notes Введенные заметки
     * @return array [bool $isValid, string $notesOrError]
     */
    public static function validateNotes(string $notes): array
    {
        $notes = trim($notes);
        
        if (mb_strlen($notes) > 200) {
            return [false, TelegramMessages::MSG_NOTES_TOO_LONG];
        }
        
        return [true, $notes];
    }

    /**
     * Валидация ID (общий метод)
     * 
     * @param mixed $id ID для проверки
     * @return bool
     */
    public static function isValidId($id): bool
    {
        return !empty($id) && is_numeric($id);
    }

    /**
     * Проверка наличия данных в массиве
     * 
     * @param array $data Данные для проверки
     * @param array $requiredKeys Обязательные ключи
     * @return array [bool $isValid, string $errorOrNull]
     */
    public static function validateData(array $data, array $requiredKeys): array
    {
        foreach ($requiredKeys as $key) {
            if (!isset($data[$key]) || empty($data[$key])) {
                return [false, "Отсутствует обязательное поле: {$key}"];
            }
        }
        return [true, null];
    }
}
