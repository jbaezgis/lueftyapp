<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;
use App\Models\Booking;
use App\Models\Location;
use App\Models\LocationAlias;
use Carbon\Carbon;

class Bookings extends Component
{
    use WithPagination;
    use Actions;

    protected $queryString = [
        'order' => ['except' => ''],
        'name' => ['except' => ''],
        'email' => ['except' => ''],
        'from' => ['except' => ''],
        'to' => ['except' => ''],
        'arrivalDate' => ['except' => ''],
        'fromDate' => ['except' => ''],
        'toDate' => ['except' => ''],
        'perPage' => ['except' => ''],
        'fromLocation' => ['except' => ''],
        'toLocation' => ['except' => ''],
        'sortField' => ['except' => ''],
        'sortDirection' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    // public $bookings;
    public $order, $name, $email, $from, $to, $arrivalDate;

    public $fromDate = '';
    public $toDate = '';
    public $fromDate2 = '';
    public $toDate2 = '';
    public $pending = '';
    public $paid = '';
    public $perPage = 10;
    public $locations = '';
    public $fromLocation = '';
    public $toLocation = '';
    public $locationAlias;
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $status = '';

    public function render()
    {
        $today = Carbon::today();
        $this->locations = Location::get();
        $this->locationAlias = LocationAlias::orderby('order_number', 'ASC')->get();

        if (!empty($this->fromDate and $this->toDate))
        {
            $this->bookingsCount = Booking::where('id', 'LIKE', "%{$this->order}%")->where('alias_location_from', 'LIKE', "%{$this->fromLocation}%")->where('alias_location_to', 'LIKE', "%{$this->toLocation}%")->where('fullname', 'LIKE', "%{$this->name}%")->where('email', 'LIKE', "%{$this->email}%")->whereBetween('arrival_date', [$this->fromDate, $this->toDate])->count();
            $this->pendingCount = Booking::where('id', 'LIKE', "%{$this->order}%")->where('alias_location_from', 'LIKE', "%{$this->fromLocation}%")->where('alias_location_to', 'LIKE', "%{$this->toLocation}%")->where('fullname', 'LIKE', "%{$this->name}%")->where('email', 'LIKE', "%{$this->email}%")->whereBetween('arrival_date', [$this->fromDate, $this->toDate])->where('status', 'pending')->count();
            $this->paidCount = Booking::where('id', 'LIKE', "%{$this->order}%")->where('alias_location_from', 'LIKE', "%{$this->fromLocation}%")->where('alias_location_to', 'LIKE', "%{$this->toLocation}%")->where('fullname', 'LIKE', "%{$this->name}%")->where('email', 'LIKE', "%{$this->email}%")->whereBetween('arrival_date', [$this->fromDate, $this->toDate])->where('status', 'paid')->count();
            $this->fromDate2 = $this->fromDate;
            $this->toDate2 = $this->toDate;


        }elseif(!empty($this->fromDate))
        {
            $this->bookingsCount = Booking::where('id', 'LIKE', "%{$this->order}%")->where('alias_location_from', 'LIKE', "%{$this->fromLocation}%")->where('alias_location_to', 'LIKE', "%{$this->toLocation}%")->where('fullname', 'LIKE', "%{$this->name}%")->where('email', 'LIKE', "%{$this->email}%")->whereBetween('arrival_date', [$this->fromDate, $today])->count();
            $this->pendingCount = Booking::where('id', 'LIKE', "%{$this->order}%")->where('alias_location_from', 'LIKE', "%{$this->fromLocation}%")->where('alias_location_to', 'LIKE', "%{$this->toLocation}%")->where('fullname', 'LIKE', "%{$this->name}%")->where('email', 'LIKE', "%{$this->email}%")->whereBetween('arrival_date', [$this->fromDate, $today])->where('status', 'pending')->count();
            $this->paidCount = Booking::where('id', 'LIKE', "%{$this->order}%")->where('alias_location_from', 'LIKE', "%{$this->fromLocation}%")->where('alias_location_to', 'LIKE', "%{$this->toLocation}%")->where('fullname', 'LIKE', "%{$this->name}%")->where('email', 'LIKE', "%{$this->email}%")->whereBetween('arrival_date', [$this->fromDate, $today])->where('status', 'paid')->count();
            $this->fromDate2 = $this->fromDate;
            $this->toDate2 = $today;


        }elseif(!empty($this->toDate))
        {
            $this->fromDate2 = date('Y-m-d', strtotime(Booking::min('arrival_date')));
            $this->toDate2 = $this->toDate;
            $this->bookingsCount = Booking::where('id', 'LIKE', "%{$this->order}%")->where('alias_location_from', 'LIKE', "%{$this->fromLocation}%")->where('alias_location_to', 'LIKE', "%{$this->toLocation}%")->where('fullname', 'LIKE', "%{$this->name}%")->where('email', 'LIKE', "%{$this->email}%")->whereBetween('arrival_date', [$this->fromDate2, $this->toDate2])->count();
            $this->pendingCount = Booking::where('id', 'LIKE', "%{$this->order}%")->where('alias_location_from', 'LIKE', "%{$this->fromLocation}%")->where('alias_location_to', 'LIKE', "%{$this->toLocation}%")->where('fullname', 'LIKE', "%{$this->name}%")->where('email', 'LIKE', "%{$this->email}%")->whereBetween('arrival_date', [$this->fromDate2, $this->toDate2])->where('status', 'pending')->count();
            $this->paidCount = Booking::where('id', 'LIKE', "%{$this->order}%")->where('alias_location_from', 'LIKE', "%{$this->fromLocation}%")->where('alias_location_to', 'LIKE', "%{$this->toLocation}%")->where('fullname', 'LIKE', "%{$this->name}%")->where('email', 'LIKE', "%{$this->email}%")->whereBetween('arrival_date', [$this->fromDate2, $this->toDate2])->where('status', 'paid')->count();

        }else {
            $this->bookingsCount = Booking::where('id', 'LIKE', "%{$this->order}%")->where('alias_location_from', 'LIKE', "%{$this->fromLocation}%")->where('alias_location_to', 'LIKE', "%{$this->toLocation}%")->where('fullname', 'LIKE', "%{$this->name}%")->where('email', 'LIKE', "%{$this->email}%")->count();
            $this->pendingCount = Booking::where('id', 'LIKE', "%{$this->order}%")->where('alias_location_from', 'LIKE', "%{$this->fromLocation}%")->where('alias_location_to', 'LIKE', "%{$this->toLocation}%")->where('fullname', 'LIKE', "%{$this->name}%")->where('email', 'LIKE', "%{$this->email}%")->where('status', 'pending')->count();
            $this->paidCount = Booking::where('id', 'LIKE', "%{$this->order}%")->where('alias_location_from', 'LIKE', "%{$this->fromLocation}%")->where('alias_location_to', 'LIKE', "%{$this->toLocation}%")->where('fullname', 'LIKE', "%{$this->name}%")->where('email', 'LIKE', "%{$this->email}%")->where('status', 'paid')->count();
            $this->fromDate2 = date('Y-m-d', strtotime(Booking::min('arrival_date')));
            $this->toDate2 = date('Y-m-d', strtotime(Booking::max('arrival_date')));

        }

        if(!empty($this->order)) {
            $this->resetPage();
        }

        return view('livewire.bookings.bookings', [
            'bookings' => Booking::where('id', 'LIKE', "%{$this->order}%")->where('alias_location_from', 'LIKE', "%{$this->fromLocation}%")->where('alias_location_to', 'LIKE', "%{$this->toLocation}%")->where('fullname', 'LIKE', "%{$this->name}%")->where('email', 'LIKE', "%{$this->email}%")->whereBetween('arrival_date', [$this->fromDate2, $this->toDate2])->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage),
        ]);

        
    }

    public function cleanFields()
    {
       $this->order = '';
       $this->name = '';
       $this->email = '';
       $this->arrivalDate = '';
       $this->fromDate = '';
       $this->toDate = '';
       $this->fromDate2 = '';
       $this->toDate2 = '';
       $this->pending = '';
       $this->paid = '';
       $this->perPage = 10;
       $this->locations = '';
       $this->fromLocation = '';
       $this->toLocation = '';
       $this->sortField = 'id';
       $this->sortDirection = 'desc';
       $this->status = '';
       $this->resetPage();
    }

    public function create()
    {
        $this->cleanFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->modal = true;
    }

    public function closeModal()
    {
        $this->modal = false;
    }

    public function edit($id)
    {
        $booking = Booking::findOrFail($id);
        $this->id_booking = $id;
        $this->name = $booking->name;
        $this->start_date = $booking->start_date;
        $this->estimate_end = $booking->estimate_end;
        $this->progress = $booking->progress;
        $this->active = 1;
        $this->status_id = $booking->status_id;
        $this->openModal();
    }

    public function delete($id)
    {
        
        Booking::find($id)->delete();
        

        $this->notification()->error(
            $title = __('booking deleted!'),
            $description = $this->id_booking ? __('booking deleted correcly.') : __('booking added correcly.')
        );

        // session()->flash('message', __('booking deleted!'));
    }

    public function save()
    {
        Booking::updateOrCreate(['id'=>$this->id_booking],
        [
            'name' => $this->name,
            'start_date' => $this->start_date,
            'estimate_end' => $this->estimate_end,
            'progress' => $this->progress,
            'active' => 1,
        ]);

        $this->notification()->success(
            $title = $this->id_booking ? __('booking updated!') : __('booking added!'),
            $description = $this->id_booking ? __('booking updated correcly.') : __('booking added correcly.')
        );

        // session()->flash('message', $this->id_booking ? __('booking updated!') : __('booking added!'));
        $this->closeModal();
        $this->cleanFields();
    }

}
