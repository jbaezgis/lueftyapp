<?php namespace App\Repositories;

use App\Models\Booking;
use App\Repositories\Repository;

class BookingRepository implements Repository{
	public $site = null;

	public function getAll(){

	}

	public function getById($id){

	}

	public function getByStatus($status){
		return Booking::where('status', $status)
		        ->orderBy('created_at', 'desc')->paginate(5000);
	}

	public function getAllTrashed(){
		return Booking::onlyTrashed()
		        ->orderBy('created_at', 'desc')
		        ->paginate(20);
	}

	public function count($status = "*"){
		$booking = Booking::orderBy('id');
		if ($status != '*')
			$booking->where('status', $status);
		return $booking->count();
	}

	public function countTrashed(){
		return Booking::onlyTrashed()->count();
	}

	public function getByKey($bookingkey){
		return Booking::where('bookingkey', $bookingkey)->first();
	}

	public function save($data){
		$request = request();
		$data['ip']				= $request->getClientIp();
		$data['site_id']		= config('Site')->id;
		$data['search_engine']	= session('searchengine');
		return Booking::create($data);
	}

	public function update($booking, $data){
		$booking->update($data);
	}
}
