<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\SiteRepository;
use App\Models\PriceOption;

class ServicePrice extends Model {
	protected $table = 'service_price';
	public $timestamps = false;
	protected $fillable = ['service_id', 'price_option_id', 'oneway_price', 'roundtrip_price'];

	public function service(){
		return $this->belongsTo('App\Models\Service');
	}

	public function priceOption(){
		return $this->belongsTo('App\Models\PriceOption', 'price_option_id');
	}

	public function option(){
		return $this->belongsTo(PriceOption::class, 'price_option_id');
	}

	public function getPriceFirstPersonAttribute($value){
	return 0;
	}

	public function getOnewayPriceAttribute($value)
    {
    	if(\Request::is('admin/*')){
    		return $value;
    	}
        
     //    $site = new SiteRepository();
     //    $currentSite = $site->getCurrentSite();

    	// if ($currentSite->is_agent == 1 ){
    	// 	$percent = $value * ($currentSite->percent / 100);
    	// 	$value += $percent;
    	// 	$value = round($value);
    	// }


    	if ($this->variation_type == 'percent') {
    		$percent = $value * ($this->variation_amount / 100);
    		$value += $percent;
    		$value = round($value);
    	}

        return number_format($value, 2,'.',',');
    }

    public function getRoundtripPriceAttribute($value)
    {
    	if(\Request::is('admin/*')){
    		return $value;
    	}
    	
        /*$site = new SiteRepository();
        $currentSite = $site->getCurrentSite();

    	if ($currentSite->is_agent == 1){
    		$percent = $value * ($currentSite->percent / 100);
    		$value += $percent;
    		$value = round($value);
    	}*/

    	if ($this->variation_type == 'percent') {
    		$percent = $value * ($this->variation_amount / 100);
    		$value += $percent;
    		$value = round($value);
    	}
    	
        return $value;
    }
}