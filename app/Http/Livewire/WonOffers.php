<?php

namespace App\Http\Livewire;

use Livewire\Component;

class WonOffers extends Component
{
    public $modal = false;
    
    public function render()
    {
        return view('livewire.won-offers')->layout('layouts.guest');
    }

    public function openModal()
    {
        $this->modal = true;
    }

    public function closeModal()
    {
        $this->modal = false;
    }
}
