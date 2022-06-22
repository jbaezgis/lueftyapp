<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\SiteRepository;
use App\Models\Location;

class Service extends Model{
	protected $table = "services";

	public $timestamps = false;

	protected $fillable = ["from", "to", "service_type", "driving_time", "price_first_person", "price_aditional_person", 
	"arrival_time", "departure_time_return", "arrival_time_return", "site_id", "price_per_children"];	

	public function prices(){
		return $this->hasMany('App\Models\ServicePrice');
	}

	public function airplainPrices(){
		return $this->hasMany('App\Models\AirplainPrice');
	}

	// public function fromlocation(){
	// 	return $this->belongsTo('App\Models\Location', 'from');
	// }

	public function fromlocation()
    {
        return $this->belongsTo(Location::class, 'from');
	}
	
	public function scopeFrom($query)
    {
        return $query->whereNotNull('from');
    }

	public function scopeTo($query)
    {
        return $query->whereNotNull('to');
	}
	
	public function tolocation(){
		return $this->belongsTo('App\Models\Location', 'to');
	}

	public function ferryHotels(){
		return $this->hasMany('App\Models\FerryShuttleHotel');
	}
	
// 	public function getPriceFirstPersonAttribute($value){
// 		$site = new SiteRepository();
// 		$currentSite = $site->getCurrentSite();
//    		if ($currentSite->is_agent == 1){
// 			$percent = $value * ($currentSite->percent / 100);
// 			$value += 30;//$percent;
// 			$value = round($value);
// 		}
//     	return number_format($value, 2,'.',',');
//    }

}