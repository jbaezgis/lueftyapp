<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;
use Carbon\Carbon;
use App\Models\Booking;

class Dashboard extends Component
{
    protected $queryString = [
        'fromDate' => ['except' => ''],
        'toDate' => ['except' => '']
    ];

    public $fromDate = '';
    public $toDate = '';
    public $pending = '';
    public $paid = '';
    // public $groundtransfers = '';
    // public $flightstransfers = '';


    public function render()
    {
        $today = Carbon::today();

        if (!empty($this->fromDate and $this->toDate))
        {
            $this->bookingsCount = Booking::whereBetween('arrival_date', [$this->fromDate, $this->toDate])->count();
            $this->pendingCount = Booking::whereBetween('arrival_date', [$this->fromDate, $this->toDate])->where('status', 'pending')->count();
            $this->paidCount = Booking::whereBetween('arrival_date', [$this->fromDate, $this->toDate])->where('status', 'paid')->count();

        }elseif(!empty($this->fromDate))
        {
            $this->bookingsCount = Booking::whereBetween('arrival_date', [$this->fromDate, $today])->count();
            $this->pendingCount = Booking::whereBetween('arrival_date', [$this->fromDate, $today])->where('status', 'pending')->count();
            $this->paidCount = Booking::whereBetween('arrival_date', [$this->fromDate, $today])->where('status', 'paid')->count();

        }else {
            $this->bookingsCount = Booking::count();
            $this->pendingCount = Booking::where('status', 'pending')->count();
            $this->paidCount = Booking::where('status', 'paid')->count();
        }
        // $this->paid = Booking::whereBetween('arrival_date', [$this->fromDate, $this->toDate])->get();
        return view('livewire.dashboard', compact('today'));
    }
}
