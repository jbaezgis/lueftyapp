<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Place;
use App\Models\LocationAlias;

class ServicesController extends Controller
{
    public function services($id)
    {
        $places = Service::where('location_id', $id)->orderBy('name')->get();

        return json_encode($places);

    }

    public function index(Request $request)
    {
        $from_id = '';
        $to_id = '';
        
        $from = $request->get('from');
        $to = $request->get('to');
        $perPage = 10;

        $locations_alias = LocationAlias::get();

        if (!empty($from and $to))
        {
            $find_from_alias = $locations_alias->where('id', $from)->first();
            $find_to_alias = $locations_alias->where('id', $to)->first();
    
            $from_id = LocationAlias::find($find_from_alias->id);
            $to_id = LocationAlias::find($find_to_alias->id);
        }


        if (!empty($from and $to))
        {
            $services = Service::where('from', $from_id->location_id)->where('to', $to_id->location_id)->where('service_type', 'groundtransfer')->from()->to()->paginate($perPage);

        }elseif (!empty($from))
        {
            $services = Service::where('from', $from_id->location_id)->where('service_type', 'groundtransfer')->from()->to()->paginate($perPage);

        }elseif (!empty($to))
        {
            $services = Service::where('to', $to_id->location_id)->where('service_type', 'groundtransfer')->from()->to()->paginate($perPage);
        }else 
        {   
            $services = Service::where('service_type', 'groundtransfer')->from()->to()->paginate($perPage);
        }


        return view('booking.transfers.services', compact('services', 'request', 'from_id', 'to_id'));
    }

    public function charters(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $perPage = 10;

        // $route = $request->get('route');
        // $search = $this->bookingHelper->ResolveLocation($this->repoLocation, $route);
        
        $aircrafts = \App\Models\Airplain::orderby('orden', 'asc')->get();

		// $metaDescription = "Charters between between {$search->fromStr} and {$search->toStr}";
		// $metaKeywords = "Charters from {$search->fromStr}, Charters to {$search->toStr}";

        if (!empty($from and $to))
        {
            $services = Service::where('from', $from)->where('to', $to)->where('service_type', 'charters')->from()->to()->paginate($perPage);

        }elseif (!empty($from))
        {
            $services = Service::where('from', $from)->where('service_type', 'charters')->from()->to()->paginate($perPage);

        }elseif (!empty($to))
        {
            $services = Service::where('to', $to)->where('service_type', 'charters')->from()->to()->paginate($perPage);
        }else 
        {   
            $services = Service::where('service_type', 'charters')->from()->to()->paginate($perPage);
        }


        return view('booking.charters.charters_services', compact('services', 'request', 'aircrafts'));
    }
}
