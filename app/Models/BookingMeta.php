<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingMeta extends Model{
	protected $table = "booking_meta";
	public $timestamps = false;

	protected $fillable = ["meta_name", "value"];	

	public function booking(){
		return $this->beglongsTo('App\Models\Booking');
	}
}
?>