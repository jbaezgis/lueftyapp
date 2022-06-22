<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model {
	protected $table = 'sites';

	protected $guarded = ['id', 'logo'];

	public function bookings(){
		return $this->hasMany('App\Models\Booking');
	}

	public function services(){
		return $this->hasMany('App\Models\Service');
	}

	public function parentSite(){
		return $this->belongsTo('App\Models\Site', 'service_instance');
	}

}
