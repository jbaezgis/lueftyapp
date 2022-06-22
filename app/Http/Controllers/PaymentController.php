<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Booking;
use Log;
use App\Services\PriceCalculator;
use App\Repositories\BookingRepository;
use Session;
use App\Events\TransferRequestedEvent;
use App\Mail\TransferConfirmation;
use Mail;
use DB;

class PaymentController extends Controller {
	public $repoBooking;

	public function __construct(BookingRepository $bookingRepository){
		parent::__construct();

		$this->repoBooking = $bookingRepository;
	}

	public function MakePaymentTransfer(Request $request, PriceCalculator $priceCalculator){
		$bookingkey = $request->get('bookingkey');
		$option = $request->get('option');
		$booking = $this->repoBooking->getByKey($bookingkey);

		if ($booking){
            $priceCalculation = $priceCalculator->groundTransfer($booking); 

			// Refresh booking prices.
			$this->repoBooking->update($booking, [
				'order_total'	=> $priceCalculation->totalFair,
				'catering'		=> $priceCalculation->catering,
				'fair'			=> $priceCalculation->price,
				'extra_payment'	=> $priceCalculation->priceAditional
			]);

			$data = [
				'title'				=> 'Ground Transfers',
				'booking'			=> $booking,
				'priceCalculation'	=> $priceCalculation,
				'option'	=> $option
			];

			return view("booking.transfers.make_payment", $data);
		} 
		else 
		{
			Log::error("Booking groundtransfer not found for key: ".$bookingkey);
			return redirect('/')->with('info', '<b>Booking number not found. <a href="/contact-us">Contact support for help</a></b>');
		}
	}

	public function MakePaymentFerryShuttle(Request $request, PriceCalculator $priceCalculator){
		$bookingkey = $request->get('bookingkey');
		$booking = $this->repoBooking->getByKey($bookingkey);
		$priceCalculation = $priceCalculator->ferryshuttle($booking);

		if ($booking) {
			// Refresh booking prices.
			$this->repoBooking->update($booking, [
				'order_total'	=> $priceCalculation->totalFair,
				'catering'		=> $priceCalculation->catering,
				'fair'			=> $priceCalculation->price,
				'extra_payment'	=> $priceCalculation->priceAditional
			]);

			$data = [
				'title'				=> 'Ferry & Shuttle',
				'booking'			=> $booking,
				'priceCalculation'	=> $priceCalculation
			];

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

			return view("booking.ferryshuttles.ferryshuttle_payment", $data);
		} 
		else 
		{
			Log::error("Booking ferryshuttle not found for key: ".$bookingkey);
			return redirect('/')->with('info', 'Booking not found');
		}
	}

	public function MakePaymentFlight(Request $request, PriceCalculator $priceCalculator){
		$bookingkey = $request->get('bookingkey');
		$booking = $this->repoBooking->getByKey($bookingkey);
		$priceCalculation = $priceCalculator->scheduleFlight($booking);

		if ($booking) {
			// Refresh booking prices.
			$this->repoBooking->update($booking, [
				'order_total'	=> $priceCalculation->totalFair,
				'catering'		=> $priceCalculation->catering,
				'fair'			=> $priceCalculation->price,
				'extra_payment'	=> $priceCalculation->priceAditional
			]);

			$data = [
				'title'				=> 'Ferry & Shuttle',
				'booking'			=> $booking,
				'priceCalculation'	=> $priceCalculation
			];

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
				//return $shuttleInfoReturn;
				$data['shuttleInfoReturn'] = $shuttleInfoReturn;
			}

			$aditionalShuttleToReturn = $booking->metas->where('meta_name', 'aditional_shuttle_to_return')->first();
			if ($aditionalShuttleToReturn){
				$shuttleInfoToReturn = \App\Models\FerryShuttleHotel::find($aditionalShuttleToReturn->value);
				$data['shuttleInfoToReturn'] = $shuttleInfoToReturn;
			}

			return view("booking.flights.make_payment", $data);
		} 
		else 
		{
			Log::error("Booking ferryshuttle not found for key: ".$bookingkey);
			return redirect('/')->with('info', 'Booking not found');
		}
	}

	public function ProcessPaymentPaypal(Request $request)
	{
		Log::info("IPN Started");
		
		// reading posted data directly from $_POST causes serialization
		// issues with array data in POST. Reading raw POST data from input stream instead.
		$raw_post_data = file_get_contents('php://input');
		$raw_post_array = explode('&', $raw_post_data);
		$myPost = array();
		foreach ($raw_post_array as $keyval) {
		    $keyval = explode ('=', $keyval);
		    if (count($keyval) == 2)
		        $myPost[$keyval[0]] = urldecode($keyval[1]);
		}
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';
		if(function_exists('get_magic_quotes_gpc')) {
		    $get_magic_quotes_exists = true;
		}
		foreach ($myPost as $key => $value) {
		    if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
		        $value = urlencode(stripslashes($value));
		    } else {
		        $value = urlencode($value);
		    }
		    $req .= "&$key=$value";
		}
		// Post IPN data back to PayPal to validate the IPN data is genuine
		// Without this step anyone can fake IPN data
		if(env('PAYPAL_SANDBOX') == true) {
		    $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		} else {
		    $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
		}
		$ch = curl_init($paypal_url);
		if ($ch == FALSE) {
		    return FALSE;
		}
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		if(env('PAYPAL_DEBUG') == true) {
		    curl_setopt($ch, CURLOPT_HEADER, 1);
		    curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
		}
		// CONFIG: Optional proxy configuration
		//curl_setopt($ch, CURLOPT_PROXY, $proxy);
		//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
		// Set TCP timeout to 30 seconds
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
		// CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
		// of the certificate as shown below. Ensure the file is readable by the webserver.
		// This is mandatory for some environments.
		//$cert = __DIR__ . "./cacert.pem";
		//curl_setopt($ch, CURLOPT_CAINFO, $cert);
		$res = curl_exec($ch);
		if (curl_errno($ch) != 0) // cURL error
			{
			    if(env('PAYPAL_DEBUG') == true) { 
	                Log::error("Can't connect to PayPal to validate IPN message: " . curl_error($ch));
				}
		    curl_close($ch);
		    exit;
		} else {
		        // Log the entire HTTP response if debug is switched on.
		        if(env('PAYPAL_DEBUG') == true) {
					/*Log::error("HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req");
                               Log::error("HTTP response of validation request: $res");*/
		        }
		        curl_close($ch);
		}
		// Inspect IPN validation result and act accordingly
		// Split response headers and payload, a better way for strcmp
		$tokens = explode("\r\n\r\n", trim($res));
		$res = trim(end($tokens));
		if (strcmp ($res, "VERIFIED") == 0) {
		    // check whether the payment_status is Completed
		    // check that txn_id has not been previously processed
		    // check that receiver_email is your PayPal email
		    // check that payment_amount/payment_currency are correct
		    // process payment and mark item as paid.
		    // assign posted variables to local variables
		    //$item_name = $_POST['item_name'];
		    $item_number = $_POST['item_number'];
		    //$payment_status = $_POST['payment_status'];
		    $payment_amount = $_POST['mc_gross'];
		    //$payment_currency = $_POST['mc_currency'];
		    //$txn_id = $_POST['txn_id'];
		    //$receiver_email = $_POST['receiver_email'];
		    //$payer_email = $_POST['payer_email'];
		    
		    $this->applyPayment($item_number, $payment_amount);

		    if(env('PAYPAL_DEBUG') == true) {
                //Log::info("Verified IPN: $req ");
                Log::info("Payment done for item: $item_number ");
		    }
		} else if (strcmp ($res, "INVALID") == 0) {
		    // log for manual investigation
		    // Add business logic here which deals with invalid IPN messages
		    if(env('PAYPAL_DEBUG') == true) {
                Log::error("Invalid IPN: $req");
		    }
		}
	}

	public function applyPayment($booking_id, $payment_amount, $payment_method='paypal'){
		$booking = Booking::findOrFail($booking_id);
		$booking->status = 'paid';
        $booking->paid_amount = $payment_amount;
		$booking->payment_method = $payment_method;
		$booking->paid_date = date('Y-m-d H:i:s');
		$booking->save();
	}

	public function thanks($booking_id, \App\Services\PriceCalculator $priceCalculator){
		$booking = Booking::findOrFail($booking_id);

		if ($booking->bookingtype == 'groundtransfer'){
			$priceCalculation = $priceCalculator->groundTransfer($booking);
		} elseif ($booking->bookingtype == 'ferryshuttle') {
			$priceCalculation = $priceCalculator->ferryShuttle($booking);
		} elseif ($booking->bookingtype == 'scheduledflights') {
			$priceCalculation = $priceCalculator->scheduleFlight($booking);
		} elseif ($booking->bookingtype == 'charters') {
			$priceCalculation = $priceCalculator->charters($booking);
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

		if ($booking->type == 'oneway'){
			$way = 'One-Way';
		} else {
			$way = 'Round-Trip';
		}

		$data['booking'] = $booking;
		$data['way'] = $way;
		$data['priceCalculation'] = $priceCalculation;

		return view('thanks', $data);
	}

    public function chargeStripe(Request $request, \App\Services\PriceCalculator $priceCalculator){
    	
        \Stripe\Stripe::setApiKey ( env('stripe_secret', 'sk_test_cMiGPAwKE7lUo1fEAaXC6DdS') );

        try {
            $paymentType = $request->input('payment_option');
            $bookingkey = $request->input('booking_key');
            $booking = $this->repoBooking->getByKey($bookingkey);
            //dd($booking);
            if(isset($booking->type)){
            	$priceCalculation = $priceCalculator->groundTransfer($booking);
            }else{
            	$priceCalculation = $priceCalculator->ferryShuttle($booking);
            }
            

            $totalToPay = 0;
            if ($paymentType == 'upfront') {
                $totalToPay = $priceCalculation->upfrontPayment;
            } else {
                $totalToPay = $priceCalculation->totalToPay;
            }

            $paymentType = ucfirst($paymentType);
            $response = \Stripe\Charge::create ( array (
                    "amount" => $totalToPay * 100,
                    "currency" => "usd",
                    "source" => $request->input ( 'stripe_token' ),
                    "description" => "{$paymentType} payment for booking #{$booking->id}" 
            ) );



            if ($response->status == 'succeeded') {
            	 
                $this->applyPayment($booking->id, $totalToPay, 'stripe');

                $bookingkey = $request->input('booking_key');
                //$bookingdata = DB::table('bookings')->where('bookingkey',$bookingkey)->get();
                // dd($booking);
                // dd($bookingdata);
                if(isset($booking->type)){
                	$price = $priceCalculator->groundTransfer($booking);
                	//dd($price);
                	// Send email
					Mail::send('emails.transfer_after_form', ['booking' => $booking, 'price' => $price], function($b) use ($booking, $price){
						$b->to($booking->email, $booking->fullname)->subject('Ground Transfer Confirmation' . ' - Order #' . $booking->id);
					});
            	}else{
            		$price = $priceCalculator->ferryShuttle($booking);
            		//dd($price);
            		// Send email
					Mail::send('emails.transfer_after_form', ['booking' => $booking, 'price' => $price], function($b) use ($booking, $price){
						$b->to($booking->email, $booking->fullname)->subject('Ferry & Shuttle Confirmation' . ' - Order #' . $booking->id);
					});
            	}
                
                return redirect('thanks/'.$booking->id);
            } else {
            	
                Log::error("Payment error: ".print_r($request->all() ." Total:". $totalToPay, true));
                return back()->with('info', '<b>There was an error with your payment, please try again or <a href="/contact-us">Contact support for help</a></b>');
            }
            
        } catch ( \Exception $e ) {
        	//dd($e);
        	
            $totalToPay = $totalToPay * 100;
            Log::error($e->getMessage());
            Log::error("Payment error: ".print_r($request->all(), true)." Total to pay: ".$totalToPay);

           return back()->with('info', $e->getMessage());
           // return back()->with('info', '<b>There was an error with your payment, please try again or <a href="/contact-us">Contact support for help</a></b>');
        }
    }
}
