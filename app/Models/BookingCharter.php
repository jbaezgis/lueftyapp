<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingCharter extends Model{
	protected $table = "booking_charters";

	protected $guarded = ['id'];	

	public function passengers(){
		return $this->hasMany('App\Models\BookingCharterPassenger', 'booking_id');
	}

	public function fromlocation(){
		return $this->belongsTo('App\Models\Location', 'from_id');
	}

	public function tolocation(){
		return $this->belongsTo('App\Models\Location', 'to_id');
	}

	public function aircraft(){
		return $this->belongsTo('App\Models\Airplain', 'aircraft_id');
	}

	public function site(){
		return $this->belongsTo('App\Models\Site');
	}
}
?>