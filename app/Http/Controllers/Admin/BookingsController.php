<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Repositories\BookingRepository;
use App\Models\Coupon;
use Mail;
use Auth;

class BookingsController extends Controller {
	public $booking;

	public function __construct(BookingRepository $booking){
		parent::__construct();

		$this->booking = $booking;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($status='paid')
	{
		if ($status == 'trash')
			$booking = $this->booking->getAllTrashed();
		else
			$booking = $this->booking->getByStatus($status);

		$booking->setPath('');

		$pendingCount = $this->booking->count('pending');
		$paidCount = $this->booking->count('paid');
		$trashCount = $this->booking->countTrashed();
		$coupons = Coupon::take(10)
		                  ->where('expiration_date', '>', new \DateTime('today'))
		                  ->orderBy('created_at', 'desc')
		                  ->get();

		return view('admin.bookings.index', [
				'bookings'	=> $booking,
				'status'	=> $status,
				'pendingCount'	=> $pendingCount,
				'paidCount'	=> $paidCount,
				'trashCount'	=> $trashCount,
				'coupons'		=> $coupons
			]);
	}

	public function charters(){
		$data['bookings'] = \App\Models\BookingCharter::orderBy('created_at', 'desc')->paginate(20);
		return view('admin.bookings.charters', $data);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show(\App\Services\PriceCalculator $priceCalculator, $id, $status = 'paid')
	{
		$booking = Booking::findOrFail($id);
		if ($booking->bookingtype == 'groundtransfer'){
			if (!$booking->servicePrice->get()){
				return 'Some of the services selected in this booking were removed.';
			}
		}
		
		$airPlane = '';

		if ($booking->bookingtype == 'groundtransfer'){
			$priceCalculation = $priceCalculator->groundTransfer($booking);
		} elseif ($booking->bookingtype == 'ferryshuttle') {
			$priceCalculation = $priceCalculator->ferryShuttle($booking);

		} elseif ($booking->bookingtype == 'scheduledflights') {
			$priceCalculation = $priceCalculator->scheduleFlight($booking);

		} elseif ($booking->bookingtype == 'charters') {
			$priceCalculation = $priceCalculator->charters($booking);
			$airPlanePrice = \App\Models\AirplainPrice::where('service_id', $booking->service->id)->first();
			$airPlane = $airPlanePrice->airplain->name;

		}

		$data = [];
		if ($booking->bookingtype == 'scheduledflights') {
				$data['shuttleInfo'] = null;
				$data['shuttleInfoReturn'] = null;
				$data['shuttleInfoTo'] = null;
				$data['shuttleInfoToReturn'] = null;

				$aditionalShuttleFrom = $booking->metas->where('meta_name', 'aditional_shuttle_from')->first();
				if ($aditionalShuttleFrom){
					$shuttleInfo = \App\Models\FerryShuttleHotel::find($aditionalShuttleFrom->value);
					$data['shuttleInfo'] = $shuttleInfo;
				}

				$aditionalShuttleTo = $booking->metas->where('meta_name', 'aditional_shuttle_to')->first();
				if ($aditionalShuttleTo){
					$shuttleInfoTo = \App\Models\FerryShuttleHotel::find($aditionalShuttleTo->value);
					$data['shuttleInfoTo'] = $shuttleInfoTo;
				}

				$aditionalShuttleFromReturn = $booking->metas->where('meta_name', 'aditional_shuttle_from_return')->first();
				if ($aditionalShuttleFromReturn){
					$shuttleInfoReturn = \App\Models\FerryShuttleHotel::find($aditionalShuttleFromReturn->value);
					$data['shuttleInfoReturn'] = $shuttleInfoReturn;
				}

				$aditionalShuttleToReturn = $booking->metas->where('meta_name', 'aditional_shuttle_to')->first();
				if ($aditionalShuttleToReturn){
					$shuttleInfoToReturn = \App\Models\FerryShuttleHotel::find($aditionalShuttleToReturn->value);
					$data['shuttleInfoToReturn'] = $shuttleInfoToReturn;
				}
			} else if ($booking->bookingtype == 'charters') {
				$data['shuttleInfo'] = null;
				$data['shuttleInfoTo'] = null;

				$aditionalShuttleFrom = $booking->metas->where('meta_name', 'aditional_shuttle_from')->first();
				if ($aditionalShuttleFrom){
					$shuttleInfo = \App\Models\FerryShuttleHotel::find($aditionalShuttleFrom->value);
					$data['shuttleInfo'] = $shuttleInfo;
				}

				$aditionalShuttleTo = $booking->metas->where('meta_name', 'aditional_shuttle_to')->first();
				if ($aditionalShuttleTo){
					$shuttleInfoTo = \App\Models\FerryShuttleHotel::find($aditionalShuttleTo->value);
					$data['shuttleInfoTo'] = $shuttleInfoTo;
				}

			}

		$data['booking']	= $booking;
		$data['status']		= $status;
		$data['priceCalculation'] = $priceCalculation;
		$data['airPlane']	= $airPlane;

		return view('admin.bookings.show', $data);
	}

	public function showcharter($id)
	{
		$booking = \App\Models\BookingCharter::findOrFail($id);
		return view('admin.bookings.showcharter', ['booking' => $booking]);
	}

	public function destroy($id){
		$booking = Booking::findOrFail($id);
		$booking->delete();
		return redirect()->back()->with('info', 'Booking removed!');
	}

	public function destroycharter($id){
		$booking = \App\Models\BookingCharter::findOrFail($id);
		$booking->delete();
		return redirect()->back()->with('info', 'Booking removed!');
	}

	public function sendcoupons(Request $request){
		$couponId = $request->input("coupon");
		$bookings = $request->input('id');
		$coupon = Coupon::find($couponId);

		foreach($bookings as $bookingId){
			$booking = Booking::find($bookingId);
			$this->sendCouponMail($booking, $coupon);
		}
		
		return redirect()->back()->with('info', 'Coupon sent!');
	}

	protected function sendCouponMail($booking, $coupon){
		$data = [
			'booking' => $booking,
			'coupon' => $coupon
		];

		Mail::send('emails.coupons', $data, function ($m) use ($booking) {
            $m->from('info@'.$booking->site->domain, $booking->site->name);

            $subject = "Discount coupon for your order";
            $email = $m->to($booking->email, $booking->firstname);
            $email->subject($subject); 
        });
	}
}
