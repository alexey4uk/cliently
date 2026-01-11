<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\Business;
use App\Services\SlugService;
use App\Traits\HasOldValues;
use Illuminate\Support\Facades\Auth;

class SlugChecker extends Component
{
    use HasOldValues;

    public $slug = '';
    public $isAvailable = null;
    public $isChecking = false;
    public $errorMessage = '';

    public function mount($value = '')
    {
        $this->slug = $value;

        if (session()->has('errors')) {
            $this->slug = $this->getOldValue('slug');
        }

        if (!empty($this->slug)) {
            $this->checkSlug();
        }
    }

    protected function rules()
    {
        return [
            'slug' => [
                'required',
                'min:3',
                'max:50',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                function ($value, $fail) {
                    if (!$this->checkSlugAvailability($value)) {
                        $fail('Этот slug уже занят. Пожалуйста, выберите другой.');
                    }
                },
            ],
        ];
    }

    public function updatedSlug($value)
    {
        $this->isAvailable = null;
        $this->errorMessage = '';
        $this->isChecking = false;

        $sanitized = Str::slug($value);

        if ($sanitized !== $value) {
            $this->slug = $sanitized;
        }

        if (empty($this->slug) || strlen($this->slug) < 3) {
            $this->isChecking = false;
            return;
        }

        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $this->slug)) {
            $this->isAvailable = false;
            $this->errorMessage = 'Только латинские буквы в нижнем регистре, цифры и одиночные дефисы. Дефисы не могут быть в начале или конце.';
            $this->isChecking = false;
            return;
        }

        $this->checkSlug();
    }

    public function checkSlug()
    {
        if (empty($this->slug) || strlen($this->slug) < 3) {
            return;
        }

        $this->isChecking = true;

        try {
            $this->validateOnly('slug');
            $this->isAvailable = true;
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->isAvailable = false;
            $this->errorMessage = $e->validator->errors()->first('slug');
        } finally {
            $this->isChecking = false;
        }
    }

    private function checkSlugAvailability($slug)
    {
        $query = Business::where('slug', $slug);

        return $query->doesntExist();
    }

    public function render()
    {
        return view('livewire.slug-checker');
    }
}
