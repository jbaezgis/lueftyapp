<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BookingMeta;
use App\Models\Service;
use App\Models\Location;
use App\Models\LocationAlias;
use DB;
use Hash;
use Log;
use App\Libraries\BookingRequestHelper;
use App\Repositories\LocationRepository;
use App\Events\FlightRequestedEvent;	
use App\Events\FerryShuttleRequestedEvent;
use App\Repositories\BookingRepository;

class BookingController extends Controller 
{
	public $repoBooking;
    public $bookingHelper;
    public $repoLocation;

	public function __construct(Request $request, BookingRequestHelper $bookingHelper, LocationRepository $repoLocation, BookingRepository $booking){
		parent::__construct();

		$this->repoBooking = $booking;
        $this->bookingHelper = $bookingHelper;
        $this->repoLocation = $repoLocation;
	}

	public function ResolveSearch(Request $request){
		$type = $request->get('type');
		$fromString = $request->get('from');
        $toString   = $request->get('to');
		$tripMode   = $request->get('trip_mode');

		if (empty($fromString) || empty($toString)) {
			return redirect('/')->with('info-search-engine', 'No service was found for the selected route, please try again or contact <a href="/contact">support</a>');
		}

		switch ($type) {
			case 'groundtransfer':
                // Redirect to pricin page
                // NOTE: Comment this to remove pricing page
                return redirect()->route('transfer_search', [
                    'route' => urlencode($fromString).'-to-'.urlencode($toString)
                ]);

                // NOTE: Uncomment this to remove pricing page
                /*
                $type = 'groundTransfer';
                $route = urlencode($fromString).'-to-'.urlencode($toString);
                $search = $this->bookingHelper->ResolveLocation($this->repoLocation, $route);

                Log::info("User viewed transfer search results between {$search->fromStr} and {$search->toStr}");
                setLog('info', 'Transfer', 'View Price', "{$search->fromStr} and {$search->toStr}");

                $results = Service::where('service_type', $type)
                    ->where('from', $search->from)
                    ->where('to', $search->to);
                
                if (count($results->get()) == 0) {
                    $search = $this->bookingHelper->ResolveLocation($this->repoLocation, $route, 'alias');
                    $results = Service::where('service_type', $type)
                     ->where('from', $search->from)
                     ->where('to', $search->to);
                }

                if ($this->currentSite->service_instance != 0){
                    $results =  $results->where('services.site_id', $this->currentSite->service_instance);
                }

                $result = $results->select(
                DB::raw('services.id, services.from, services.to, services.driving_time, services.mapimage, services.arrival_time,
                    (SELECT location_name FROM locations WHERE locations.id=services.from) AS from_name,
                    (SELECT location_name FROM locations WHERE locations.id=services.to) AS to_name')
                )->first();

                if ( !$result ){
                    Log::error("No results found for route {$search->fromStr} to {$search->toStr} on server: {$this->currentSite->domain}");
                    return redirect('/')->with('info-search-engine', 'No service was found for the selected route, please try again or contact <a href="/contact">support</a>');
                }

                $formUrl = "request-ground-transfer-service?service=$result->id&way=$tripMode";

                if (isset($search->aliasFromID))
                    $formUrl .= "&aFrom=$search->aliasFromID";

                if (isset($search->aliasToID))
                    $formUrl .= "&aTo=$search->aliasToID";

				return redirect($formUrl);
                */
               
				break;
			
			case 'scheduledflights':
                // Comment this to remove pricing page
                return redirect()->route('flight_search', [
                    'route' => urlencode($fromString).'-to-'.urlencode($toString)
                ]);

                // Uncomment this if remove pricing page
                /*
                $type = 'scheduledflights';
                $route = urlencode($fromString).'-to-'.urlencode($toString);
                $search = $this->bookingHelper->ResolveLocation($this->repoLocation, $route);

                $results = Service::where('service_type', $type)
                                ->where('from', $search->from)
                                ->where('to', $search->to);

                if ($this->currentSite->service_instance != 0){
                    $results =  $results->where('services.site_id', $this->currentSite->service_instance);
                }

                $result = $results->select(
                        DB::raw('services.id, services.from, services.to, services.driving_time, services.mapimage, services.arrival_time,
                            (SELECT location_name FROM locations WHERE locations.id=services.from) AS from_name,
                            (SELECT location_name FROM locations WHERE locations.id=services.to) AS to_name')
                    )->first();

                if ( !$result ){
                    $fromString = isset($fromString) ? $fromString : '';
                    $toString = isset($toString) ? $toString : '';
                    Log::error("No results found for route {$fromString} to {$toString} on server: {$this->currentSite->domain}");
                    return redirect('/')->with('info-search-engine', 'No service was found for the selected route, please try again or contact <a href="/contact">support</a>');
                }

				$formUrl = "request-flight-service?service=$result->id&way=$tripMode";
                return redirect($formUrl);*/

				break;

			case 'ferryshuttle':
				return redirect('ferryshuttle-service');
				break;

			case 'charters':
				return redirect("charters-results?route={$fromString}-to-{$toString}");
				break;

			default:
				abort(403);
				break;
		}
	}

	public function charters(Request $request){
		$from = $request->get('from');
		$to = $request->get('to');

		if ($from == 0 || $to == 0){
			return redirect()->back();
		}

		$service = Service::where('service_type', 'charters')
						  ->where('site_id', $this->currentSite->service_instance)
		                  ->where('from', $from)
		                  ->where('to', $to)
		                  ->firstOrFail();

		$aircrafts = \App\Models\Airplain::orderby('orden', 'asc')->get();
		return view('booking.select_charters', compact('aircrafts', 'service'));
	}

	public function charterRequest(Request $request){
		$from = $request->get('from');
		$to = $request->get('to');
		$aircraftID = $request->get('aircraft');

		$fromlocation = \App\Models\Location::find($from);
		$tolocation = \App\Models\Location::find($to);
		$aircraft = \App\Models\Airplain::find($aircraftID);

		return view('booking.form_charters', compact('from', 'to', 'aircraftID', 'fromlocation', 'tolocation', 'aircraft'));
	}

	public function charterStore(Request $request, \App\Services\Notifications $notification){
		$this->validate($request, [
			'fullname'		=> 'required|max:250',
			'email'			=> 'required|email|max:250',
			'phone'			=> 'required|max:50',
			'flight_date'	=> 'date'
		]);

		// $input = $request->except(['passenger_firstname', 'passenger_lastname', 'passenger_nationality', 'passenger_passport_number', 'service_type',
		// 	'flight_hour','flight_minutes', 'flight_meridiam']);  
		$input = $request->all();

		$input['search_engine']	= session('searchengine');
		$input['ip']	= $request->getClientIp();
		$input['site_id']		= $this->currentSite->id;
		$input['flight_departure_time'] 	= $input['flight_hour'].':'.$input['flight_minutes'].' '.$input['flight_meridiam'];

		$booking = \App\Models\BookingCharter::create($input);
		$hashstring = 'secret-@#-'.$booking->id;
		$booking->bookingkey = Hash::make($hashstring);
		$booking->save();

		
		//Passengers information
		$firstname = $request->input('passenger_firstname');
		$lastname = $request->input('passenger_lastname');
		$nationality = $request->input('passenger_nationality');
		$passport = $request->input('passenger_passport_number');

		if (is_array($firstname) && count($firstname) > 0) {
			$passengersObject = [];

			for ($i = 0; $i < count($firstname); $i++) {
				$passenger = \App\Models\BookingCharterPassenger::create([
					'first_name'	=> $firstname[$i],
					'last_name'		=> $lastname[$i],
					'nationality'	=> $nationality[$i],
					'passport'		=> $passport[$i],
					'passenger_type'			=> 'passenger'
				]);

				$booking->passengers()->save($passenger);
			}
		}

		$firstnameInfant = $request->input('infant_firstname');
		$lastnameInfant = $request->input('infant_lastname');
		$nationalityInfant = $request->input('infant_nationality');
		$passportInfant = $request->input('infant_passport_number');

		if (is_array($firstnameInfant) && count($firstnameInfant) > 0) {
			$passengersInfantObject = [];

			for ($i = 0; $i < count($firstnameInfant); $i++) {
				if (empty($firstnameInfant[$i])){continue;}
				$passenger = \App\Models\BookingCharterPassenger::create([
					'first_name'	=> $firstnameInfant[$i],
					'last_name'		=> $lastnameInfant[$i],
					'nationality'	=> $nationalityInfant[$i],
					'passport'		=> $passportInfant[$i],
					'passenger_type'			=> 'infant'
				]);

				$booking->passengers()->save($passenger);
			}
		}

		$notification->charters($booking);

		return redirect("charter-request-confirmation/?bookingkey=".$booking->bookingkey);
	}

	public function CharterRequestConfirmation(Request $request){
		$bookingkey = $request->get('bookingkey');
		$booking = \App\Models\BookingCharter::where('bookingkey', $bookingkey)->firstOrFail();

		$data['booking'] = $booking;
		return view("booking.confirm_charters", $data);
	}


	/**
	 * 
	 *
	* @param  \Illuminate\Http\Request $request [<description>]
	 */
    public function CompleteBooking(Request $request) {
        $bookingKey = $request->get('bookingkey');
        $booking = \App\Models\Booking::where('bookingKey', $bookingKey)->firstOrFail();

        $paymentUrl= '';

        switch ($booking->bookingtype) {
		    case "groundtransfer":
		       $paymentUrl = route("transfer_payment");
		        break;
		    case "ferryshuttle":
		       $paymentUrl = "ferryshuttle_payment";
		        break;
		    case "scheduledflights":
		      $paymentUrl = "flight_payment";
		        break;
		}

		$paymentUrl .= '?bookingkey='.$bookingKey; //."#payment"

		return view('booking.complete_booking', [
											'bookingkey' => $bookingKey,
											'paymentUrl' => $paymentUrl
										]);
    }
}
