<?php

namespace App\Livewire;

use App\Traits\HasOldValues;
use Livewire\Component;
use Livewire\Attributes\Computed;

class PhoneInput extends Component
{
    use HasOldValues;

    public $phone = '+375';
    public $label;
    public $name;
    public $required;
    public $placeholder;
    public $error = '';

    public function mount($required = false, $label = null, $name = 'phone', $placeholder = '+375 (__) ___-__-__', $value = '')
    {
        $this->required = $required;
        $this->label = $label;
        $this->name = $name;
        $this->placeholder = $placeholder;

        // Приоритет: old input -> переданное значение -> дефолт
        $old = $this->getOldValue($name);
        $this->phone = $old ?? ($value ?: '+375');
    }

    /**
     * Свойство $cleanPhone теперь вычисляется автоматически
     */
    #[Computed]
    public function cleanPhone()
    {
        $digits = preg_replace('/\D/', '', $this->phone);
        
        // Если номер начинается с 375, ограничиваем 12 цифрами, иначе добавляем префикс
        if (!str_starts_with($digits, '375') && !empty($digits)) {
            $digits = '375' . $digits;
        }

        return substr($digits, 0, 12);
    }

    public function render()
    {
        return view('livewire.phone-input');
    }
}
