<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Repositories\ServiceRepository;
use App\Repositories\BookingRepository;
use App\Models\FerryShuttleHotel;
use App\Libraries\BookingRequestHelper;
use App\Repositories\PageRepository;
use App\Events\FerryShuttleRequestedEvent;
use App\Services\PriceCalculator;

use App\Mail\FerryConfirmation;
use Mail;
use Hash;

class FerryShuttleController extends Controller
{
	public $repoService;
	public $bookingHelper;
	public $repoBooking;
	public $pageRepo;

	function __construct(ServiceRepository $service, BookingRepository $booking, BookingRequestHelper $bookingHelper, PageRepository $pageRepository) {
		parent::__construct();

		$this->repoService = $service;
		$this->repoBooking = $booking;
		$this->pageRepo = $pageRepository;

		$this->bookingHelper = $bookingHelper;
		$this->bookingHelper->init();
	}

    public function FerryshuttlePage(){
    	$page = $this->pageRepo->getRestPageBySlug('ferryshuttle');

		if (!$page) {
			abort(404);
		}

		return view('booking.ferryshuttles.ferryshuttle_page', [
			'page' => $page,
			'metaDescription' => $page->metaDescription,
			'metaKeywords' => $page->metaKeywords
		]);
	}

	public function OptionalFerryShuttle(){
		return view('booking.ferryshuttles.ferryshuttle_optional_page');
	}

	public function SelectHotel($service_id){
		$service = $this->repoService->getById($service_id);
		return view('booking.ferryshuttles.ferryshuttle_hotel', compact('service'));
	}

	public function RequestForm(Request $request)
	{
		$hotelId = $request->get('hotel');
		$service_id = $request->get('service_id');
		$service = $this->repoService->getById($service_id);
		$hotel = FerryShuttleHotel::findOrFail($hotelId);
		$shuttleSamana = FerryShuttleHotel::where('service_id', 31)->get();

		$hours = $this->bookingHelper->getHours();

		return view('booking.ferryshuttles.ferryshuttle_form', compact('service', 'hotel', 'hours', 'shuttleSamana'));
	}

	public function SubmitRequest(Request $request, \App\Services\PriceCalculator $priceCalculator)
	{
		$this->validate($request, [
			'fullname'		=> 'required|max:150',
			'email'			=> 'required|email|max:250',
			'phone'			=> 'required|max:50',
			'arrival_date'	=> 'date'
		]);

		$data = $request->all();  
		$data['bookingtype'] = $request->service_type;
		$data['service_id'] = $request->service_id;
		
		$booking = $this->repoBooking->save($data);
		$service = $this->repoService->getById($request->service_id);

		$hashstring = 'secret-@#-'.$booking->id;
		$booking->bookingkey = Hash::make($hashstring);
		$booking->save();

		$hotelId = $request->input('hotel_id');
		$hotel = \App\Models\FerryShuttleHotel::find($hotelId);
		
		// Save metas
		$booking->saveMeta('pickup_time', $request->pickup_time);
		$booking->saveMeta('from_information', $request->from_information);
		$booking->saveMeta('to_information', $request->to_information);
		$booking->saveMeta('other_information', $request->other_information);
		$booking->saveMeta('hotel', $hotel->hotel);
		$booking->saveMeta('hotel_name', $request->hotel);

		if ($request->has('interested_overnight')){
			$booking->saveMeta('Interested in Overnight', $request->interested_overnight);
		}

		if ($request->has('excursion')){
			$booking->saveMeta('excursion', $request->excursion);
		}

		if ($service->tolocation->location_name == 'Samana' && $request->has('shuttle_destination')) {
			$booking->saveMeta('shuttle_destination', $request->shuttle_destination);
		}

        $priceCalculation = $priceCalculator->ferryshuttle($booking);

        $this->repoBooking->update($booking, [
            'order_total'   => $priceCalculation->totalFair,
            'catering'      => $priceCalculation->catering,
            'fair'          => $priceCalculation->price,
            'extra_payment' => $priceCalculation->priceAditional,
            'price_aditional_passenger' => $priceCalculation->priceAditional
        ]);

        event(new FerryShuttleRequestedEvent($booking));
        //dd($booking);
        Mail::to($booking->email)->send(new FerryConfirmation($booking));
        Mail::to($booking->site->email)->send(new FerryConfirmation($booking));
       
		return redirect('make-payment-ferry-shuttle/?bookingkey='.$booking->bookingkey);
	}
}
