<?php

namespace App\Livewire;

use App\Traits\HasOldValues;
use Livewire\Component;

class TextareaInput extends Component
{
    use HasOldValues;

    public $value = '';

    public $name;

    public $placeholder = '';

    public $rows = 3;

    public $maxlength = 500;

    public $required = false;

    public $showCounter = true;

    public $resize = 'none';

    public $label = null;

    public function mount(
        $name,
        $value = '',
        $label = null,
        $placeholder = '',
        $rows = 3,
        $maxlength = 500,
        $required = false,
        $showCounter = true,
        $resize = 'none'
    ) {
        $this->name = $name;

        if (session()->has('errors')) {
            $this->value = $this->getOldValue($name, $value);
        } else {
            $this->value = $value;
        }

        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->rows = $rows;
        $this->maxlength = $maxlength;
        $this->required = $required;
        $this->showCounter = $showCounter;
        $this->resize = $resize;
    }

    public function updatedValue($value)
    {
        //
    }

    public function getCharacterCount()
    {
        return mb_strlen($this->value);
    }

    public function getCounterClasses()
    {
        $characterCount = $this->getCharacterCount();
        $percentage = ($characterCount / $this->maxlength) * 100;

        if ($percentage >= 100) {
            return 'text-rose-600 dark:text-rose-400 font-semibold';
        } elseif ($percentage >= 90) {
            return 'text-rose-600 dark:text-rose-400';
        } elseif ($percentage >= 80) {
            return 'text-amber-600 dark:text-amber-400';
        }

        return 'text-slate-400 dark:text-slate-500';
    }

    public function render()
    {
        return view('livewire.textarea-input', [
            'characterCount' => $this->getCharacterCount(),
            'counterClasses' => $this->getCounterClasses(),
            'isLimitExceeded' => $this->getCharacterCount() > $this->maxlength,
        ]);
    }
}
