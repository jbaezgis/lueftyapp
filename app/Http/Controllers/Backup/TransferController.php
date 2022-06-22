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
use App\Events\TransferRequestedEvent;
use App\Services\PriceCalculator;
use App\Mail\TransferConfirmation;
use Mail;
use DB;
use Log;
use Hash;
use App\Repositories\PageRepository;
use App\Place;
// use Carbon\Carbon;

class TransferController extends Controller
{
	public $pageRepo;

	public $type = 'groundtransfer';
	public $bookingHelper;
	public $repoLocation;
    public $repoBooking;

	// public function __construct(Request $request, PageRepository $pageRepository, BookingRequestHelper $bookingHelper, LocationRepository $repoLocation, BookingRepository $repoBooking){
	// 	parent::__construct();

	// 	$this->bookingHelper = $bookingHelper;
	// 	$this->repoLocation = $repoLocation;

	// 	$this->bookingHelper->init($request, $this->repoLocation);
	// 	$this->repoBooking = $repoBooking;
	// 	$this->pageRepo = $pageRepository;
	// }

    /**
     * Remove this method in the future, it is not being used
     * 
     * @param Request $request [description]
     * @param [type]  $route   [description]
     */
	public function SearchResults(Request $request, $route){
		$search = $this->bookingHelper->ResolveLocation($this->repoLocation, $route);
		Log::info("User viewed transfer search results between {$search->fromStr} and {$search->toStr}");
		setLog('info', 'Transfer', 'View Price', "{$search->fromStr} and {$search->toStr}");

		$results = Service::where('service_type', $this->type)
		->where('from', $search->from)
		->where('to', $search->to);
		
		if (count($results->get()) == 0) {
			$search = $this->bookingHelper->ResolveLocation($this->repoLocation, $route, 'alias');
			$results = Service::where('service_type', $this->type)
		     ->where('from', $search->from)
		     ->where('to', $search->to);
		}

		// if ($this->currentSite->service_instance != 0){
		// 	$results = 	$results->where('services.site_id', $this->currentSite->service_instance);
		// }

		$results = $results->select(
		DB::raw('services.id, services.from, services.to, services.driving_time, services.mapimage, services.arrival_time,
			(SELECT location_name FROM locations WHERE locations.id=services.from) AS from_name,
			(SELECT location_name FROM locations WHERE locations.id=services.to) AS to_name')
		)->get();

		if (count($results) == 0){
			Log::error("No results found for route {$search->fromStr} to {$search->toStr} on server: dominicanshuttles.com");
			return redirect('/')->with('info-search-engine', 'No service was found for the selected route, please try again or contact <a href="/contact">support</a>');
		}

		$metaDescription = "See our prices for private transfer between {$search->fromStr} and {$search->toStr}";
		$metaKeywords = "transfer from {$search->fromStr}, transfer to {$search->toStr}";

		return view('booking.transfers.search', [
			'results' => $results,
			'fromString' => $search->fromStr,
			'toString' => $search->toStr,
			'aliasFromID' => $search->aliasFromID,
			'aliasToID' => $search->aliasToID,
			'metaDescription' => $metaDescription,
			'metaKeywords' => $metaKeywords
		]);
	}

    /**
     * Display the transfer form
     *     
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
	public function showForm(Request $request){
		$way = $request->get('way');
		$aFrom = $request->get('aFrom');
		$aTo = $request->get('aTo');

		if (!in_array($way, ['oneway', 'roundtrip'])){
			abort(403, 'Way not provided');
		}

		$service = Service::with('prices')->where('id', $request->get('service'));

		// if ($this->currentSite->service_instance != 0){
		// 	$service = 	$service->where('services.site_id', $this->currentSite->service_instance);
		// }

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

		setLog('info', 'Transfer', 'View Form', "{$service->from_name} and {$service->to_name}");

		$willArriveData = [
			'90' => '1 hour 30 min',
			'120' => '2 hours 00 min',
			'150' => '2 hours 30 min',
			'180' => '3 hours 00 min',
			'210' => '3 hours 30 min'
		];

		// $d = '2006-12-12';
		// $date = date('Y', strtotime('+1 year', $d));
		// // $date->modify('+1 day');
		$s_time = $service->driving_time;

		$pickup_time1 = date('H:i',strtotime('+14 hour -20 minutes',strtotime($s_time)));

		$view = $service->service_type;
		$metaDescription = "{$way} request from {$service->from_name}";
		return view("booking.transfers.form", [
			'service'		=> $service,
			'service_type'	=> $service->service_type,
			'way'			=> $way,
			'hours'			=> $this->bookingHelper->getHours(),
			'childSeatPrice'	=> config('app.childSeatPrice'),
			'willArriveData' => $willArriveData,
			'metaDescription' => $metaDescription,
			'aliasFromID' => $service->aliasFromID,
			'aliasToID' => $service->aliasToID,
			'pickup_time1' => $pickup_time1,
		]);
	}

    /**
     * Submit the transfer form
     *     
     * @param  Request $request
     * @param  PriceCalculator $priceCalculator
     * @return \Illuminate\Http\Response
     */
	public function submitForm(Request $request, PriceCalculator $priceCalculator) {
		$rules = [
			'fullname'		=> 'required|max:150',
			'email'			=> 'required|email|max:250',
			'phone'			=> 'required|max:50',
			'arrival_date'	=> 'required|date_format:"Y-m-d"',
			// 'from_information'	=> 'required|max:250',
			// 'to_information'	=> 'required|max:250'
			'service_price_id' => 'required',
			// 'language' => 'language',

		];

		$type = $request->input('type');
		if ($type == 'roundtrip' && !$request->has('choose_same_information')){
			// $rules['return_date'] = 'required|date';
			// $rules['return_from_information'] = 'required';
			$rules['service_price_id'] = 'required';
		}

		$messages = [
			'return_date.required' => 'Date required',
			'arrival_date.required' => 'Date required',
			'from_information.required' => 'From information required',
			'to_information.required' => 'To information required',
			'return_from_information.required' => 'From information required',
			'return_to_information.required' => 'To information required',
			'service_price_id.required' => 'Please select how many passengers',
		];

		//$this->validate($request, $rules, $messages);
		$validator = \Validator::make($request->all(), $rules, $messages);

		if ($validator->fails()) {
			Log::warning("Validation failed for transfer ".print_r($request->all(), true));
			setLog('warning', 'Transfer', 'Validation failed', json_encode($request->toArray()));

			return redirect()->back()
			->withErrors($validator)
			->withInput();
		}

		$input = $request->all();
		

		$service = Service::findOrFail($input['service_id']);

		$input['bookingtype']	= $input['service_type'];
		$input['service_id'] 	= $input['service_id'];
		$input['child_seat'] 	= $input['childseats_upto12month'];
		$input['child_seat_1year'] = $input['childseats_over1year'];

		// if ($request->has('arrival_hour')){
		// 	$input['arrival_time'] 	= $input['arrival_hour'].':'.$input['arrival_minutes'].' '.$input['arrival_meridiam'];
		// }

		

		if ($type == 'roundtrip' && $request->has('return_hour')){
			$input['return_time'] 	= $input['return_hour'].':'.$input['return_minutes'].' '.$input['return_meridiam'];
		}

		$input['ip']			= $request->getClientIp();
		$input['site_id']		= $this->currentSite->id;
		$input['search_engine']	= session('searchengine');
		
		$booking = Booking::create($input);
		$hashstring = 'secret-@#-'.$booking->id;
		$booking->bookingkey = Hash::make($hashstring);
		$booking->phone_dr = $request->phone_dr;
		$booking->more_information = $request->more_information;
		$booking->language = $request->language;

		// Places
		$booking->from_place = $request->from_place;
		$booking->to_place = $request->to_place;

		$booking->arrival_time = date('H:i:s', strtotime($request->arrival_time));
		if ($request->has('arrival_time')){
		}

		if ($request->has('arrival_airline'))
		{
			$booking->arrival_airline = $request->arrival_airline;
			$booking->flight_number = $request->flight_number;
		}

		
		// Save metas
		// $booking->saveMeta('arrival_airline', $request->arrival_airline);
		// $booking->saveMeta('flight_number', $request->flight_number);
		
		// if ($request->has('want_to_arrive')){
		// 	$wantToArriveParts = explode(":", $input['want_to_arrive']);
		// 	$wantToArrive = $wantToArriveParts[0]." hour(s) ".$wantToArriveParts[1]." minutes";
		// 	$booking->saveMeta('want_to_arrive', $wantToArrive);
		// }
		
		// $booking->saveMeta('pickup_time', $request->pickup_time);
		// $booking->saveMeta('pickme_time', $request->pickme_time);
		// $booking->saveMeta('from_information', $request->from_information);
		// $booking->saveMeta('to_information', $request->to_information);
		
		if ($request->has('want_to_arrive'))
		{
			$booking->want_to_arrive = $request->want_to_arrive;
	
			// Calculo de pick up time One way
			$s_time_a = $service->driving_time_minutes;
			//dd($service);
			$pickup_time_oneway_1 = date('H:i',strtotime("-$s_time_a minutes",strtotime($request->arrival_time)));
			$pickup_time_oneway_2 = date('H:i',strtotime("-$request->want_to_arrive minutes",strtotime($pickup_time_oneway_1)));
	
			$booking->pickup_time = date('H:i',strtotime("-15 minutes",strtotime($pickup_time_oneway_2)));
		}

		if ($type == 'roundtrip'){
			// $booking->saveMeta('return_airline', $request->return_airline);
			// $booking->saveMeta('return_flight_number', $request->return_flight_number);
			
			// Cambios realizados por Yoel
			$booking->return_airline = $request->return_airline;
			
			$booking->return_flight_number = $request->return_flight_number;

			$booking->return_want_to_arrive_2 = $request->return_want_to_arrive_2;

			if ($request->has('return_time_2'))
			{
				$booking->return_time_2 = date('H:i:s', strtotime($request->return_time_2));

				$s_time = $service->driving_time_minutes;

				$pickup_time1 = date('H:i',strtotime("-$s_time minutes",strtotime($request->return_time_2)));
				$pickup_time2 = date('H:i',strtotime("-$request->return_want_to_arrive_2 minutes",strtotime($pickup_time1)));

				$booking->return_pickup_time_2 = date('H:i',strtotime("-15 minutes",strtotime($pickup_time2)));
				
			}
			// end Yoel

			if ($request->has('return_want_to_arrive')){
				$wantToArriveParts = explode(":", $request->return_want_to_arrive);
				$wantToArrive = $wantToArriveParts[0]." hour(s) ".$wantToArriveParts[1]." minutes";
				$booking->metas()->save( new BookingMeta(['meta_name' => 'return_want_to_arrive', 'value' => $wantToArrive ]) );
				$booking->saveMeta('return_want_to_arrive', $wantToArrive);
			}
			

			// $booking->saveMeta('return_pickup_time', $request->return_pickup_time);
			// $booking->saveMeta('return_pickme_time', $request->return_pickme_time);

			if ($request->has('choose_same_information')){
				if ($service->tolocation->is_airport || $service->fromlocation->is_airport){
					if ($service->tolocation->is_airport)
						$message = 'The Arrival Information is Same as Departure Information';
					else
						$message = 'The Departure Information is Same as Arrival Information';
				} else {
					$message = 'The Return Travel Information is Same as Onward Travel Information';
				}

				$booking->saveMeta('choose_same_information', $message);
			}

			$booking->return_more_information = $request->return_more_information;

			// $booking->saveMeta('return_from_information', $request->return_from_information);
			// $booking->saveMeta('return_to_information', $request->return_to_information);
		}



		setLog('info', 'Transfer', 'Request sent', 'booking key: '.$booking->bookingkey.' -- '.json_encode($request->toArray()) );

        // // Ask for products only for SDQ and PUJ   
		// if ($booking->service->from == 2 || $booking->service->from == 1){
		// 	return redirect('products/ask?bookingkey='.$booking->bookingkey);

		// } else {
        //     // If any other location, just confirm the booking
        //     $price = $priceCalculator->groundTransfer($booking);
        //     return $this->confirmBooking($booking, $price);
		// }

		$price = $priceCalculator->groundTransfer($booking);
		
		// return $this->confirmBooking($booking, $price);

		// $url = "make-payment-transfer?bookingkey="

		
		// Fire new booking request event for transfer
		
		$booking->save();
		
		// event(new TransferRequestedEvent($booking));
		
		// Send email
		//dd($booking);
		// Send email
		Mail::send('emails.transfer_after_form_ds', ['booking' => $booking, 'price' => $price], function($b) use ($booking, $price){
			$b->to('info@dominicanshuttles.com')->subject('Ground Transfer Confirmation' . ' - Order #' . $booking->id);
		});
		
		Mail::send('emails.transfer_after_form', ['booking' => $booking, 'price' => $price], function($b) use ($booking, $price){
			$b->to($booking->email, $booking->fullname)->subject('Ground Transfer Confirmation' . ' - Order #' . $booking->id);
		});

		// return redirect()->route('make-payment-transfer?bookingkey='.$booking->bookingkey);
		// return $this->confirmBooking($booking, $price);
		return redirect('make-payment-transfer?bookingkey='.$booking->bookingkey);
	}

    /**
     * Confirm a transfer
     * This is a confirmation when a user is adding a product or a tour
     * Accesed by post
     *
     * @param  \Illuminate\Http\Request $request [<description>]
     * @param  PriceCalculator $priceCalculator
     * @return \Illuminate\Http\Response
     */
    public function confirm(Request $request, PriceCalculator $priceCalculator) {
        $bookingkey = $request->input('bookingkey');
        $booking = $this->repoBooking->getBykey($bookingkey);

        if ( $request->has('cancel_catering') ) {
            $booking->products()->detach();
            Log::info('Catering canceled!');
        }

        if ( !$booking ) abort(404);

        $price = $priceCalculator->groundTransfer($booking);

        $isAjax = $request->ajax();
        return $this->confirmBooking($booking, $price, $isAjax);
    }

    /**
     * Cancel Catering and confirm a transfer
     *
     * @return \Illuminate\Http\Response
     */
    public function cancelCatering() {
        
    }

    /**
     * Internal function to confirm booking
     * Dispatch event and send the email confirmation 
     * redirect to payment form
     *
     * @acccess protected
     * @param  App\Models\Booking $booking 
     * @param  App\Services\PriceCalculator $price
     * @return \Illuminate\Http\Response
     */
    protected function confirmBooking($booking, $price, $isAjax = false) {
        // Update totals
        $this->repoBooking->update($booking, [
            'order_total'   => $price->totalFair,
            'catering'      => $price->catering,
            'fair'          => $price->price,
            'extra_payment' => $price->priceAditional 
        ]);

        if ($booking->email_sent == 0){
            // Fire new booking request event for transfer
            event(new TransferRequestedEvent($booking));

            // Send email
            Mail::to($booking->email)->send(new TransferConfirmation($booking));
            Mail::to($booking->site->email)->send(new TransferConfirmation($booking));

            $booking->email_sent = 1;
            $booking->update();
        }

        // $paymentRoute = 'make-payment-transfer/?bookingkey='.$booking->bookingkey;


         $paymentRoute = 'complete-booking/?bookingkey='.$booking->bookingkey;

        if ( $isAjax ) {
            return url( $paymentRoute );
        }

        return redirect($paymentRoute);
	}
	
	private function getLocationData($type){
		$data = new \stdClass();
		$data->$type = new \stdClass();
		$shortname = $type;

		if ($shortname == 'groundtransfer'){
				$From = DB::select(DB::raw("
					SELECT l.location_name as from_name, l.id AS from_id, l.order_number FROM services s
					INNER JOIN locations l ON l.id=s.from
					WHERE s.service_type='groundtransfer'
					GROUP BY location_name
					UNION
					SELECT location_name as from_name, location_id AS from_id, order_number FROM location_alias
					ORDER BY order_number ASC, from_name ASC
					"));
				$data->$shortname->from = $From;

				$To = DB::select(DB::raw("
					SELECT l.location_name as to_name, l.id AS to_id, l.order_number FROM services s
					INNER JOIN locations l ON l.id=s.to
					WHERE s.service_type='groundtransfer'
					GROUP BY location_name
					UNION
					SELECT location_name as to_name, location_id AS to_id, order_number FROM location_alias
					ORDER BY order_number ASC, to_name ASC
					"));
				$data->$shortname->to = $To;

			} else {

		$From = Service::select(
						DB::raw('services.id, services.from, 
							(SELECT location_name FROM locations WHERE locations.id=services.from) AS from_name,
							(SELECT order_number FROM locations WHERE locations.id=services.from) AS loc_order'))
						->where('services.service_type', $shortname)
						->where('services.site_id', $this->currentSite->service_instance)
						->orderBy('loc_order', 'ASC')
						->orderBy('from_name', 'ASC')
						->groupBy('services.from')
	                    ->get();
	    $data->$shortname->from = $From;

	    $From = Service::select(
						DB::raw('services.id, services.from, services.to,
							(SELECT location_name FROM locations WHERE locations.id=services.to) AS to_name,
							(SELECT order_number FROM locations WHERE locations.id=services.to) AS loc_order'))
						->where('services.service_type', $shortname)
						->where('services.site_id', $this->currentSite->service_instance)
						->orderBy('loc_order', 'ASC')	
						->orderBy('to_name', 'ASC')	
						->groupBy('services.to')
	                    ->get();
	    $data->$shortname->to = $From;
	}
	    return $data;
	}

	public function transfers()
	{
		$data = $this->getLocationData('groundtransfer');
		$page = $this->pageRepo->getRestPageBySlug('ground-transfers');

		if (!$page) {
			abort(404);
		}
		
		return view('transfers.index', [
			'page' => $page,
			'metaDescription' => $page->metaDescription,
			'metaKeywords' => $page->metaKeywords
		])->with(['servicesJson' => json_encode($data)]);
	}

	public function transfersResults(Request $request, $route)
	{
		$search = $this->bookingHelper->ResolveLocation($this->repoLocation, $route);
		Log::info("User viewed transfer search results between {$search->fromStr} and {$search->toStr}");
		setLog('info', 'Transfer', 'View Price', "{$search->fromStr} and {$search->toStr}");

		$results = Service::where('service_type', $this->type)
		->where('from', $search->from)
		->where('to', $search->to);
		
		if (count($results->get()) == 0) {
			$search = $this->bookingHelper->ResolveLocation($this->repoLocation, $route, 'alias');
			$results = Service::where('service_type', $this->type)
		     ->where('from', $search->from)
		     ->where('to', $search->to);
		}

		// if ($this->currentSite->service_instance != 0){
		// 	$results = 	$results->where('services.site_id', $this->currentSite->service_instance);
		// }

		$results = $results->select(
		DB::raw('services.id, services.from, services.to, services.driving_time, services.mapimage, services.arrival_time,
			(SELECT location_name FROM locations WHERE locations.id=services.from) AS from_name,
			(SELECT location_name FROM locations WHERE locations.id=services.to) AS to_name')
		)->get();

		if (count($results) == 0){
			Log::error("No results found for route {$search->fromStr} to {$search->toStr} on server: dominicanshuttles.com");
			return redirect('/')->with('info-search-engine', 'No service was found for the selected route, please try again or contact <a href="/contact">support</a>');
		}

		$metaDescription = "See our prices for private transfer between {$search->fromStr} and {$search->toStr}";
		$metaKeywords = "transfer from {$search->fromStr}, transfer to {$search->toStr}";

		// $findmybid = $bids->where('auction_id', $auction_id)->where('user_id', auth()->user()->id)->first();
		// $mybid = Bid::find($findmybid->id);
			
		return view('transfers.transfers-results', [
			'results' => $results,
			'fromString' => $search->fromStr,
			'toString' => $search->toStr,
			'aliasFromID' => $search->aliasFromID,
			'aliasToID' => $search->aliasToID,
			'metaDescription' => $metaDescription,
			'metaKeywords' => $metaKeywords
		]);	
	}


}