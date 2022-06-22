<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;
use App\Models\Booking;
use App\Models\Location;
use App\Models\LocationAlias;
use App\Models\Service;
use Carbon\Carbon;

class BookingDetails extends Component
{
    protected $queryString = [
        'bookingkey' => ['except' => '']
    ];

    public $bookingkey, $booking;

    public function mount($id)
    {
        // $this->booking = Booking::find($id);
        $this->booking = Booking::where('id', $id)->where('bookingkey', $this->bookingkey)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.booking-details');
    }
}
