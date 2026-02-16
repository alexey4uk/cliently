<?php

namespace App\Livewire;

use App\Models\Business;
use App\Traits\HasOldValues;
use Illuminate\Support\Str;
use Livewire\Component;

class SlugChecker extends Component
{
    use HasOldValues;

    public $slug = '';

    public $isAvailable = null;

    public $isChecking = false;

    public $errorMessage = '';

    public $businessId = null;

    public function mount($value = '', $businessId = null)
    {
        $this->businessId = $businessId;

        // Используем ваш трейт для получения старых данных при ошибках формы
        $this->slug = session()->has('errors')
            ? $this->getOldValue('slug')
            : $value;

        if (strlen($this->slug) >= 3) {
            $this->checkSlug();
        }
    }

    /**
     * Срабатывает при каждом обновлении $slug через wire:model
     */
    public function updatedSlug($value)
    {
        // Принудительно форматируем слаг (нижний регистр, дефисы)
        $this->slug = Str::slug($value);

        $this->isAvailable = null;
        $this->errorMessage = '';

        if (strlen($this->slug) < 3) {
            $this->isChecking = false;

            return;
        }

        $this->checkSlug();
    }

    public function checkSlug()
    {
        $this->isChecking = true;

        // Прямая проверка в БД без создания объекта Validator
        $isTaken = Business::where('slug', $this->slug)
            ->when($this->businessId, fn ($q) => $q->where('id', '!=', $this->businessId))
            ->exists();

        if ($isTaken) {
            $this->isAvailable = false;
            $this->errorMessage = 'Этот адрес уже занят';
        } else {
            $this->isAvailable = true;
            $this->errorMessage = '';
        }

        $this->isChecking = false;
    }

    public function render()
    {
        return view('livewire.slug-checker');
    }
}
