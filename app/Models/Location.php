<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model{
	protected $table = "locations";

	public $timestamps = false;

	protected $fillable = [ "location_name", "is_airport"];	

	public function getIsAirportAttribute($value){
		return $value == 1 ? true : false;
	}

	public function services(){
		return $this->hasMany('App\Models\Service', 'from');
	}
}
?>