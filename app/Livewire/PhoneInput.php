<?php

namespace App\Livewire;

use App\Traits\HasOldValues;
use Livewire\Component;

class PhoneInput extends Component
{
    use HasOldValues;

    public $phone;
    public $value;
    public $error = '';
    public $cleanPhone = '';
    public $label;
    public $name = '';
    public $required;
    public $placeholder;

    private $validOperatorCodes = ['29', '33', '44', '25'];
    private $requiredDigits = 9;

    public function mount(
        $required = false, 
        $label = null,
        $name = '',
        $placeholder = '',
    ){
        $this->required = $required;
        $this->label = $label;
        $this->placeholder = $placeholder;

        if (session()->has('errors')) {
            $this->phone = $this->getOldValue($name, $this->phone);
        }
    }
    
    public function updatedPhone($value)
    {
        $this->error = '';
        
        // Если поле пустое
        if (empty($value)) {
            $this->phone = '+375';
            return;
        }
        
        // Преобразуем ввод: если начинается с цифры оператора, добавляем +375
        if (!str_starts_with($value, '+375')) {
            // Проверяем, начинается ли ввод с допустимой цифры оператора
            if (preg_match('/^[234]/', $value)) {
                $value = '+375' . $value;
            } else {
                $this->phone = '+375';
                return;
            }
        }
        
        // Защита от удаления префикса
        if (strlen($value) < 4 || !str_starts_with($value, '+375')) {
            $this->phone = '+375';
            return;
        }
        
        // Извлекаем цифры после +375
        $digits = $this->extractDigits($value);
        
        // Ограничиваем до 9 цифр
        if (strlen($digits) > $this->requiredDigits) {
            $digits = substr($digits, 0, $this->requiredDigits);
        }
        
        // Проверка кода оператора
        if (!$this->validateOperatorCode($digits)) {
            return;
        }
        
        // Формируем итоговое значение
        $this->phone = '+375' . $digits;

        $this->cleanPhone = $this->getCleanPhoneProperty();
    }
    
    public function onFocus()
    {
        // При фокусе гарантируем наличие префикса
        if (empty($this->phone) || !str_starts_with($this->phone, '+375')) {
            $this->phone = '+375';
        }
    }
    
    private function extractDigits($value)
    {
        $withoutPrefix = substr($value, 4);
        return preg_replace('/\D/', '', $withoutPrefix);
    }
    
    private function validateOperatorCode($digits)
    {
        // Если нет цифр - всё ок
        if (strlen($digits) < 1) {
            return true;
        }
        
        $firstDigit = substr($digits, 0, 1);
        
        // Проверка первой цифры
        if (!in_array($firstDigit, ['2', '3', '4'])) {
            $this->error = 'Неверный код оператора. Допустимые: 29, 33, 44, 25';
            $this->phone = '+375';
            return false;
        }
        
        // Проверка полного кода оператора (если введено 2+ цифры)
        if (strlen($digits) >= 2) {
            $operatorCode = substr($digits, 0, 2);
            if (!in_array($operatorCode, $this->validOperatorCodes)) {
                // Оставляем только первую правильную цифру
                $this->error = 'Неверный код оператора. Допустимые: 29, 33, 44, 25';
                $this->phone = '+375' . $firstDigit;
                return false;
            }
        }
        
        return true;
    }
    
    public function onBlur()
    {
        $digits = $this->extractDigits($this->phone);
        
        // Проверка на полный номер при потере фокуса
        if (str_starts_with($this->phone, '+375') && strlen($digits) < $this->requiredDigits) {
            $this->error = 'Введите полный номер телефона (9 цифр после +375)';
        } elseif (str_starts_with($this->phone, '+375')) {

        }
    }
    
    public function getCleanPhoneProperty()
    {
        if (empty($this->phone)) {
            return '';
        }
        
        $clean = preg_replace('/\D/', '', $this->phone);
        
        // Ограничиваем до 12 цифр (375 + 9)
        if (strlen($clean) > 12) {
            $clean = substr($clean, 0, 12);
        }
        
        if (str_starts_with($clean, '375')) {
            return $clean;
        }
        
        return '375' . $clean;
    }
    
    public function getFormattedPhoneProperty()
    {
        if (empty($this->phone)) {
            return '';
        }
        
        $digits = $this->extractDigits($this->phone);
        
        // Если номер полный - форматируем
        if (strlen($digits) === $this->requiredDigits) {
            $operatorCode = substr($digits, 0, 2);
            $firstPart = substr($digits, 2, 3);
            $secondPart = substr($digits, 5, 2);
            $thirdPart = substr($digits, 7, 2);
            
            return sprintf('+375 (%s) %s-%s-%s', 
                $operatorCode, 
                $firstPart, 
                $secondPart, 
                $thirdPart
            );
        }
        
        // Если номер неполный - показываем как есть
        return $this->phone;
    }
    
    public function render()
    {
        return view('livewire.phone-input', [
            'formattedPhone' => $this->formattedPhone,
        ]);
    }
}