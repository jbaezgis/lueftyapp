<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Repositories\BookingRepository;
use App\Models\Coupon;
use Mail;
use Auth;

class ExcelController extends Controller
{
    public $booking;

	public function __construct(BookingRepository $booking){
		parent::__construct();

		$this->booking = $booking;
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
        
        $thismonth = Booking::whereRaw('MONTH(created_at) = '.$month)->sum('paid_amount');

		return view('admin.dt.index', [
				'bookings'	=> $booking,
				'status'	=> $status,
				'pendingCount'	=> $pendingCount,
				'paidCount'	=> $paidCount,
				'trashCount'	=> $trashCount,
				'coupons'		=> $coupons
			]);
    }
}
