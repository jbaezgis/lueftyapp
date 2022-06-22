<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Service;
use App\Models\LocationAlias;
use App\Models\BookingMeta;
use App\Http\Requests;
use App\Libraries\BookingRequestHelper;
use App\Repositories\LocationRepository;
use App\Repositories\BookingRepository;
use App\Events\CharterRequestedEvent;
use App\Models\BookingCharter;
use App\Models\BookingCharterPassenger;
use Hash;
use DB;
use Mail;

class ChartersController extends Controller
{
    public $type = 'charters';
	public $bookingHelper;
	public $repoLocation;
	public $repoBooking;

	public function __construct(Request $request, BookingRequestHelper $bookingHelper, 
		LocationRepository $repoLocation, BookingRepository $booking){
		parent::__construct();

		$this->bookingHelper = $bookingHelper;
		$this->repoLocation = $repoLocation;
		$this->repoBooking = $booking;

		$this->bookingHelper->init($request, $this->repoLocation);
	}

	public function SearchResults(Request $request)
	{
		$route = $request->get('route');
		$search = $this->bookingHelper->ResolveLocation($this->repoLocation, $route);


		if ($search->from == 0 || $search->to == 0){
			return redirect('/')->with('info-search-engine', 'No service was found for the selected route, please try again or contact <a href="/contact">support</a>');
		}

		$service = Service::where('service_type', 'charters')
						  ->where('site_id', $this->currentSite->service_instance)
		                  ->where('from', $search->from)
		                  ->where('to', $search->to)
		                  ->firstOrFail();

		$aircrafts = \App\Models\Airplain::orderby('orden', 'asc')->get();

		$metaDescription = "Charters between between {$search->fromStr} and {$search->toStr}";
		$metaKeywords = "Charters from {$search->fromStr}, Charters to {$search->toStr}";

		return view('booking.charters.charter_select', [
			'aircrafts' => $aircrafts, 
			'service' => $service,
			'metaDescription' => $metaDescription,
			'metaKeywords' => $metaKeywords
		]);
	}

	public function RequestForm(Request $request)
	{
		$from = $request->get('from');
		$to = $request->get('to');
		$aircraftID = $request->get('aircraft');

		$fromlocation = \App\Models\Location::find($from);
		$tolocation = \App\Models\Location::find($to);
		$aircraft = \App\Models\Airplain::find($aircraftID);

		return view('booking.charters.charters_form', compact('from', 'to', 'aircraftID', 'fromlocation', 'tolocation', 'aircraft'));
	}

	public function SubmitRequest(Request $request)
	{
		$this->validate($request, [
			'fullname'		=> 'required|max:250',
			'email'			=> 'required|email|max:250',
			'phone'			=> 'required|max:50',
			'flight_date'	=> 'date'
		]);

		$flightDepartureTime 	= $request->flight_hour.':'.$request->flight_minutes.' '.$request->flight_meridiam;

		$booking = BookingCharter::create([
			'from_id'	=> $request->from_id,
			'to_id'		=> $request->to_id,
			'fullname'	=> $request->fullname,
			'email'		=> $request->email,
			'phone'		=> $request->phone,
			'phone_dr'	=> $request->phone_dr,
			'flight_date' => $request->flight_date,
			'infants'	=> $request->infants,
			'infants_over2years'	=> $request->infants_over2years,
			'flight_departure_time'	=> $request->flight_departure_time,
			'searchengine'	=> session('searchengine'),
			'ip'			=> $request->getClientIp(),
			'site_id'		=> $this->currentSite->id,
			'aircraft_id'	=> $request->aircraft_id,
			'passengers'	=> $request->passengers
		]);

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
				$passenger = new BookingCharterPassenger([
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
				$passenger = new BookingCharterPassenger([
					'first_name'	=> $firstnameInfant[$i],
					'last_name'		=> $lastnameInfant[$i],
					'nationality'	=> $nationalityInfant[$i],
					'passport'		=> $passportInfant[$i],
					'passenger_type'			=> 'infant'
				]);

				$booking->passengers()->save($passenger);
			}
		}

		event(new CharterRequestedEvent($booking));
		// dd($booking);
		$from = DB::table('locations')->where('id', $booking->from_id)->get();
		$to = DB::table('locations')->where('id', $booking->to_id)->get();
		$vehicle = DB::table('airplains')->where('id', $booking->aircraft_id)->get();
		//dd($booking->email);
		
		$data = [
			'fullname' => $booking->fullname,
			'vehicle' => $vehicle[0]->name,
			'from' =>  $from[0]->location_name,
			'to' =>  $to[0]->location_name,
			'date' =>  $booking->flight_date,
			'email' => $booking->email,
			'phone' => $booking->phone
		];
		//dd($data);

		Mail::send('emails.aircraft_request', $data, function ($m) use ($data) {
            $m->from('info@dominicanshuttles.com');
            $m->to($data['email']);
            $m->bcc('info@dominicanshuttles.com');
            $m->subject('New Aircraft Booking Request');
		});

		Mail::send('emails.aircraft_request', $data, function ($m) use ($data) {
            $m->from($data['email']);
            $m->to('info@dominicanshuttles.com');
            $m->bcc('info@dominicanshuttles.com');
            $m->subject('New Aircraft Booking Request');
		});

		return redirect("charter-request-confirmation/?bookingkey=".$booking->bookingkey);
	}

	public function CharterRequestConfirmation(Request $request)
	{
		$bookingkey = $request->get('bookingkey');
		$booking = \App\Models\BookingCharter::where('bookingkey', $bookingkey)->firstOrFail();

		$data['booking'] = $booking;
		return view("booking.charters.charter_confirmation", $data);
	}
}
