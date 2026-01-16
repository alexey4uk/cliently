<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class StatsCard extends Component
{
    public $label;
    public $value;
    public $icon;
    public $color;

    public function mount($label, $value, $icon, $color)
    {
        $this->label = $label;
        $this->value = $value;
        $this->icon = $icon;
        $this->color = $color;
    }

    public function render()
    {
        return view('livewire.dashboard.stats-card');
    }
}
