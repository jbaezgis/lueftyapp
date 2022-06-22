<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Libraries\BookingRequestHelper;
use App\Repositories\LocationRepository;
use App\Services\PriceCalculator;
use App\Repositories\BookingRepository;

use App\Models\Service;
use App\Models\LocationAlias;
use App\Models\Booking;

use App\Events\FlightRequestedEvent;

use App\Mail\FlightConfirmation;   

use DB;
use Log;
use Hash;
use Mail;

class FlightsController extends Controller
{
	public $type = 'scheduledflights';
	public $bookingHelper;
	public $repoLocation;

	public function __construct(Request $request, BookingRequestHelper $bookingHelper, LocationRepository $repoLocation, BookingRepository $booking){
		parent::__construct();

		$this->bookingHelper = $bookingHelper;
		$this->repoLocation = $repoLocation;
        $this->repoBooking = $booking;

		$this->bookingHelper->init($request, $this->repoLocation);
	}

    public function SearchResults(Request $request, $route){
    	$search = $this->bookingHelper->ResolveLocation($this->repoLocation, $route);

		$results = Service::where('service_type', $this->type)
						->where('from', $search->from)
						->where('to', $search->to);

		if ($this->currentSite->service_instance != 0){
			$results = 	$results->where('services.site_id', $this->currentSite->service_instance);
		}

		$results = $results->select(
				DB::raw('services.id, services.from, services.to, services.driving_time, services.mapimage, services.arrival_time,
					(SELECT location_name FROM locations WHERE locations.id=services.from) AS from_name,
					(SELECT location_name FROM locations WHERE locations.id=services.to) AS to_name')
			)->get();

		if (count($results) == 0){
			$fromString = isset($fromString) ? $fromString : '';
			$toString = isset($toString) ? $toString : '';
			Log::error("No results found for route {$fromString} to {$toString} on server: {$this->currentSite->domain}");
			return redirect('/')->with('info-search-engine', 'No service was found for the selected route, please try again or contact <a href="/contact">support</a>');
		}

		$metaDescription = "See our prices for flights between {$search->fromStr} and {$search->toStr}";
		$metaKeywords = "Regular flight from {$search->fromStr}, Regular flight to {$search->toStr}";

		return view('booking.flights.search', [
			'results' => $results,
			'fromString' => $search->fromStr,
			'toString' => $search->toStr,
			'aliasFromID' => $search->aliasFromID,
			'aliasToID' => $search->aliasToID,
			'metaDescription' => $metaDescription,
			'metaKeywords' => $metaKeywords
		]);
    }

    public function RequestForm(Request $request){
    	$way = $request->get('way');
		$aFrom = $request->get('aFrom');
		$aTo = $request->get('aTo');

		if (!in_array($way, ['oneway', 'roundtrip'])){
			abort(403, 'Way not provided');
		}

		$service = Service::with('prices')->where('id', $request->get('service'));

		if ($this->currentSite->service_instance != 0){
			$service = 	$service->where('services.site_id', $this->currentSite->service_instance);
		}

		$service = $service->select(
					DB::raw('services.id, services.from, services.to, services.driving_time, services.arrival_time, services.departure_time_return,
							arrival_time_return, services.service_type, services.price_per_children,
							(SELECT location_name FROM locations WHERE locations.id=services.from) AS from_name,
							(SELECT location_name FROM locations WHERE locations.id=services.to) AS to_name')
				)
				->firstOrFail();

		if ($aFrom != 0){
			$aliasFrom = LocationAlias::findOrFail($aFrom);
			$service->from_name = $aliasFrom->location_name;
		}

		if ($aTo != 0){
			$aliasTo = LocationAlias::findOrFail($aTo);
			$service->to_name = $aliasTo->location_name;
		}

		$willArriveData = [
			'1:30' => '1 hour(s) 30 min',
			'2:00' => '2 hours 00 min',
			'2:30' => '2 hours 30 min',
			'3:00' => '3 hours 00 min',
			'3:30' => '3 hours 30 min'
		];

		$view = $service->service_type;

		$shuttlesFromDestionationAirport = $service->ferryHotels()->where('way', 'from')->get();
		$shuttlesToDestionationAirport = $service->ferryHotels()->where('way', 'to')->get();

		return view("booking.flights.form", [
				'service'		=> $service,
				'service_type'	=> $service->service_type,
				'way'			=> $way,
				'hours'			=> $this->bookingHelper->getHours(),
				'childSeatPrice'	=> config('app.childSeatPrice'),
				'willArriveData' => $willArriveData,
				'shuttlesFromDestionationAirport' => $shuttlesFromDestionationAirport,
				'shuttlesToDestionationAirport' => $shuttlesToDestionationAirport
		]);
    }

    public function SubmitRequest(Request $request, PriceCalculator $priceCalculator){
    	$this->validate($request, [
			'fullname'		=> 'required|max:150',
			'email'			=> 'required|email|max:250',
			'phone'			=> 'required|max:50',
			'arrival_date'	=> 'required|date'
		]);

		$input = $request->all();

		$servicePriceId = $input['service_price_id'];
		$servicePrice = \App\Models\ServicePrice::find($servicePriceId);
		$priceOption = \App\Models\PriceOption::find($servicePrice->price_option_id);

		$input['bookingtype'] = $input['service_type'];
		$input['service_id'] = $input['service_id'];
		$input['ip']	= $request->getClientIp();
		$input['site_id']		= $this->currentSite->id;
		$input['passengers']	= $priceOption->maxpassengers;
		$input['search_engine']	= session('searchengine');
		$booking = Booking::create($input);

		$hashstring = 'secret-@#-'.$booking->id;
		$booking->bookingkey = Hash::make($hashstring);
		$booking->save();

		// Passengers information
		$firstname = $request->input('passenger_firstname');
		$lastname = $request->input('passenger_lastname');
		$nationality = $request->input('passenger_nationality');
		$passport = $request->input('passenger_passport_number');

		if (is_array($firstname) && count($firstname) > 0) {
			$passengersObject = [];

			for ($i = 0; $i < count($firstname); $i++) {
				\App\Models\Passenger::create([
					'booking_id'	=> $booking->id,
					'first_name'	=> $firstname[$i],
					'last_name'		=> $lastname[$i],
					'nationality'	=> $nationality[$i],
					'passport'		=> $passport[$i],
					'passenger_type' => 'passenger'
				]);
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
				$passenger = \App\Models\Passenger::create([
					'first_name'	=> $firstnameInfant[$i],
					'last_name'		=> $lastnameInfant[$i],
					'nationality'	=> $nationalityInfant[$i],
					'passport'		=> $passportInfant[$i],
					'passenger_type'			=> 'infant'
				]);

				$booking->passengers()->save($passenger);
			}
		}

		// Metas
		$booking->saveMeta('hotel_pickup_time', $request->input('hotel_pickup_time'));
		$booking->saveMeta('hotel_pickup_time_return', $request->input('hotel_pickedup_return'));
		$booking->saveMeta('aditional_shuttle_from', $request->input('aditional_shuttle_from'));
		$booking->saveMeta('hotel_to_be_pickup_from', $request->input('hotel_to_be_pickup_from'));
		$booking->saveMeta('international_arrival_from', $request->input('international_arrival_from'));

		$booking->saveMeta('aditional_shuttle_to', $request->input('aditional_shuttle_to'));
		$booking->saveMeta('hotel_to_be_pickup_to', $request->input('hotel_to_be_pickup_to'));
		$booking->saveMeta('international_arrival_to', $request->input('international_arrival_to'));

		$booking->saveMeta('aditional_shuttle_from_return', $request->input('aditional_shuttle_from_return'));
		$booking->saveMeta('hotel_to_be_pickup_from_return', $request->input('hotel_to_be_pickup_from_return'));
		$booking->saveMeta('international_arrival_from_return', $request->input('international_arrival_from_return'));

		$booking->saveMeta('aditional_shuttle_to_return', $request->input('aditional_shuttle_to_return'));
		$booking->saveMeta('hotel_to_be_pickup_to_return', $request->input('hotel_to_be_pickup_to_return'));

        $priceCalculation = $priceCalculator->scheduleFlight($booking);

        // Confirm the booking
        $this->repoBooking->update($booking, [
            'order_total'   => $priceCalculation->totalFair,
            'catering'      => $priceCalculation->catering,
            'fair'          => $priceCalculation->price,
            'extra_payment' => $priceCalculation->priceAditional
        ]);

        event(new FlightRequestedEvent($booking));

        Mail::to($booking->email)->send(new FlightConfirmation($booking));
        Mail::to($booking->site->email)->send(new FlightConfirmation($booking));

		return response()->json(['booking_key' => $booking->bookingkey]);
    }
}
