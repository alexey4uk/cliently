<?php

namespace App\Livewire;

use App\Traits\HasOldValues;
use Livewire\Component;
use Illuminate\Support\Str;

class TextInput extends Component
{
    use HasOldValues;

    public $value = '';
    public $name;
    public $type = 'text';
    public $placeholder = '';
    public $required = false;
    public $modifier = null;
    public $wireModel = 'value';
    public $label = null;

    public function mount(
        $name,
        $value = '',
        $type = 'text',
        $placeholder = '',
        $required = false,
        $modifier = null,
        $wireModel = null,
        $label = null
    ) {
        $this->name = $name;
        $this->value = $this->getOldValue($name, $value);
        $this->type = $type;
        $this->placeholder = $placeholder;
        $this->required = $required;
        $this->modifier = $modifier;
        $this->label = $label;
        $this->wireModel = $wireModel ?? 'value';
    }

    public function updatedValue($value)
    {
        if (empty($value) || !$this->modifier) {
            return;
        }

        // Применяем модификатор
        $modified = $this->applyModifier($value);

        // Обновляем только если значение изменилось
        if ($modified !== $value) {
            $this->value = $modified;
        }
    }

    private function applyModifier($value)
    {
        return match ($this->modifier) {
            'capitalize' => Str::ucfirst(Str::lower($value)),
            'title' => Str::title($value),
            'upper' => Str::upper($value),
            'lower' => Str::lower($value),
            'ucfirst' => Str::ucfirst($value),
            'ucwords' => Str::ucwords($value),
            default => $value,
        };
    }

    public function render()
    {
        return view('livewire.text-input');
    }
}
