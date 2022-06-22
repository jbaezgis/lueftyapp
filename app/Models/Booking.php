<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BookingMeta;
use App\Place;

class Booking extends Model{
	use SoftDeletes;

	protected $table = "bookings";

	protected $fillable = [
		"bookingtype", 
		"from_place", 
		"to_place", 
		"service_id", 
		"service_price_id", 
		"fullname", 
		"email", 
		"phone", 
		"phone_dr",
		"passengers", 
		"child_seat", 
		"child_seat_1year", 
		"bookingkey", 
		"type",
		"arrival_airline",
		"flight_number",
		"arrival_date",
		"return_date",
		"arrival_time",
		"return_time",
		"return_time_2",
		"want_to_arrive",
		"return_want_to_arrive_2",
		"pickup_time",
		"return_pickup_time_2",
		"more_information",
		"return_more_information",
		"ip",
		"fair",
		"order_total",
		"catering",
		"site_id",
		"search_engine",
		"coupon_text",
		"alias_location_from",
		"alias_location_to",
		"hotel_id",
		"nationality",
		"language",
	];	

	protected $dates = ["created_at", "updated_at", "arrival_date", "return_date", "paid_date"];

	/**
	 * An booking has many metas
	 */
	public function metas(){
		return $this->hasMany('App\Models\BookingMeta');
	}

	/**
	 * Get product asociated with booking
	 */
	public function products(){
		return $this->belongsToMany('App\Models\Product')->withPivot('qty', 'id')->withTimestamps();
	}

	/**
	 * A booking depends on a service
	 */
	public function service(){
		return $this->belongsTo('App\Models\Service');
	}

	/**
	 * Each booking has a price variation
	 */
	public function servicePrice(){
		return $this->belongsTo('App\Models\ServicePrice', "service_price_id");
	}

	public function site(){
		return $this->belongsTo('App\Models\Site');
	}

	public function passengers(){
		return $this->hasMany('App\Models\Passenger');
	}

	public function SaveMeta($metaName, $value){
		if ($value){
			$this->metas()->save( new BookingMeta([
				'meta_name' => $metaName, 
				'value' => $value
			]));
		}
	}

	public function getMeta($key){
		$meta = $this->metas->where('meta_name', $key)->first();
		if ($meta)
			return $meta->value;
		else
			return "";
	}

	public function hasMeta($key){
		return $this->metas->where('meta_name', $key)->first();
	}

	public function fromplace()
    {
        return $this->belongsTo(Place::class, 'from_place');
	}

	public function toplace()
    {
        return $this->belongsTo(Place::class, 'to_place');
	}
}


?>