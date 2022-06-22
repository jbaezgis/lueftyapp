<?php

namespace App\Http\Livewire;

use Livewire\Component;
use WireUi\Traits\Actions;
use App\Models\Booking;
use App\Models\Location;
use App\Models\LocationAlias;
use App\Models\Service;
use Carbon\Carbon;

class BookingCreate extends Component
{
    use Actions;
    
    public function render()
    {
        return view('livewire.booking-create');
    }
}
