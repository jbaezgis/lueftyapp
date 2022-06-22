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

class BookingForm extends Component
{
    use WithPagination;
    use Actions;
    
    protected $queryString = [
        'bookingkey' => ['except' => '']
    ];

    public $id_booking, $booking, $fullname, $email, $phone, $language, $arrival_date, $arrival_time, $arrival_airline, $arrival_flight, $more_information;
    public $bookingkey;
    public $type = 'oneway';
    public $showDiv = false;
    public $willArriveData;

    protected $rules = [
        'fullname' => 'required',
        'email' => 'required|email',
        'phone' => 'required|min:10',
        'language' => 'required',
        'arrival_date' => 'required',
    ]; 

    public function mount($id)
    {
        // $this->booking = Booking::find($id);
        $this->booking = Booking::where('id', $id)->where('bookingkey', $this->bookingkey)->firstOrFail();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {

        $this->willArriveData = [
			'90' => '1 hour 30 min',
			'120' => '2 hours 00 min',
			'150' => '2 hours 30 min',
			'180' => '3 hours 00 min',
			'210' => '3 hours 30 min'
		];
        return view('livewire.booking-form');
    }

    public function save()
    {
        Booking::updateOrCreate(['id'=>$this->id_booking],
        [
            'fullname' => $this->fullname,
            'email' => $this->email,
            'phone' => $this->phone,
            'language' => $this->language,
            'arrival_date' => $this->arrival_date,
            'arrival_time' => $this->arrival_time,
            'arrival_airline' => $this->arrival_airline,
            'arrival_flight' => $this->arrival_flight,
            'more_information' => $this->more_information,
        ]);

        $this->notification()->success(
            $title = $this->id_booking ? __('Booking updated!') : __('booking added!'),
            $description = $this->id_booking ? __('booking updated correcly.') : __('Project added correcly.')
        );

        // return redirect()->to('/booking-details');
    }

    public function openDiv()
    {
        $this->showDiv =! $this->showDiv;
    }

}
