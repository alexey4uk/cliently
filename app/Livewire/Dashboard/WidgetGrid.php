<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class WidgetGrid extends Component
{
    public $widgets = [];

    public $widgetOrder = [];

    public $appointments = [];

    public $clients = [];

    public $stats = [];

    public $business = null;

    public function mount($widgets, $widgetOrder, $appointments, $clients, $stats, $business = null)
    {
        $this->widgets = $widgets;
        $this->widgetOrder = $widgetOrder;
        $this->appointments = $appointments;
        $this->clients = $clients;
        $this->stats = $stats;
        $this->business = $business;
    }

    public function render()
    {
        return view('livewire.dashboard.widget-grid');
    }
}
