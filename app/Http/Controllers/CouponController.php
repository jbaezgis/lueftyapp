<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Coupon;
use App\Models\Booking;
use DateTime;

class CouponController extends Controller {
	public function applyCoupon(Request $request){
		$code = $request->input('code');
		$bookingId = $request->input('booking_id');
		$coupon = Coupon::where('code', $code)
		                ->where('expiration_date', '>', new DateTime('today'))
		                ->first();
		if (!$coupon){
			return redirect()->back()->with('info', 'Coupon has expired or is invalid');
		}

		$booking = Booking::findOrFail($bookingId);
		$booking->coupon = $code;
		$booking->discount = $this->getDiscount($coupon, $booking);
		$booking->coupon_text = $coupon->type == 'percent' ? $coupon->discount.'% off' : 'U$ '.$coupon->discount.' off';
		$booking->save();

		return redirect()->back()->with('info', 'Coupon applied!');
	}	

	/**
	 * Apply a coupon directly by URL
	 */
	public function applyCouponFromEmail(Request $request){
		$code = $request->get('code');
		$bookingKey = $request->get('booking_key');
		$coupon = Coupon::where('code', $code)
		                ->where('expiration_date', '>', new DateTime('today'))
		                ->first();
		if (!$coupon){
			return redirect('booking/makepayment?bookingkey='.$bookingKey)->with('info', 'Coupon has expired or is invalid');
		}

		$booking = Booking::where('bookingkey', $bookingKey)->first();
		$booking->coupon = $code;
		$booking->discount = $this->getDiscount($coupon, $booking);
		$booking->coupon_text = $coupon->type == 'percent' ? $coupon->discount.'% off' : 'U$ '.$coupon->discount.' off';
		$booking->save();

		return redirect('booking/makepayment?bookingkey='.$bookingKey)->with('info', 'Coupon applied!');
	}

	public function removeCoupon(Request $request, $bookingId){
		$booking = Booking::find($bookingId);
		$booking->coupon = '';
		$booking->discount = 0;
		$booking->save();

		return redirect()->back()->with('info', 'Coupon removed!');
	}

	protected function getDiscount($coupon, $booking){
		if ($coupon->type == 'percent'){
			$discount = ($booking->order_total / 100) * $coupon->discount;
			return number_format($discount, 2,'.','');
		} else {
			return number_format($coupon->discount, 2,'.',',');
		}
	}
}