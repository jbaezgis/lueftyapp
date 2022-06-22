<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;
use App\Models\Booking;
use App\Models\Location;
use App\Models\LocationAlias;
use App\Models\Service;
use App\Models\ServicePrice;
use Carbon\Carbon;
use App\Http\Requests;

class Home extends Component
{
    protected $queryString = [
        // 'type' => ['except' => ''],
        'fromLocation' => ['except' => ''],
        'toLocation' => ['except' => ''],
        'arrivalDate' => ['except' => ''],
        'passengers' => ['except' => '']
    ];

    public $title; 
    public $fromLocation = '';
    public $locAliasFrom = '';
    public $toLocation = '';
    public $locAliasTo = '';
    public $service = '';
    public $sevicePrices = '';
    public $locAlias = '';
    public $arrivalDate = '';
    public $passengers = 1;
    // public $type = 'oneway';
    
    public function render()
    {
        $this->locAlias = LocationAlias::orderBy('order_number', 'ASC')->get();
        $this->locAliasFrom = LocationAlias::where('id', $this->fromLocation)->first();
        $this->locAliasTo = LocationAlias::where('id', $this->toLocation)->first();
        if (!empty($this->fromLocation and $this->toLocation))
        {
            $this->service = Service::where('from', $this->locAliasFrom->location_id)->where('to', $this->locAliasTo->location_id)->first();
        }

        if($this->service)
        {
            $this->servicePrices = ServicePrice::where('service_id', $this->service->id)->where('status', 'Active')->whereHas('priceOption', function ($query) {
                $query->where('maxpassengers', '>=', $this->passengers);
            })->get();
        }
        
        return view('livewire.home');
    }

    public function cleanFields()
    {
        $this->fromLocation = '';
        $this->toLocation = '';
        $this->arrivalDate = '';
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
