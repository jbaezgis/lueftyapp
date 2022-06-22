<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingCharterPassenger extends Model{
	protected $table = "booking_charters_passengers";
	public $timestamps = false;

	protected $guarded = ["id"];	

	public function BookingCharter(){
		return $this->belongsTo('App\Models\BookingCharter');
	}
}
?>