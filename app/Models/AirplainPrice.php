<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Airplain;
use App\Models\Service;
class AirplainPrice extends Model {

	protected $table = 'airplains_prices';
	public $timestamps = false;
	protected $guarded = ['id'];

	public function airplain(){
		return $this->belongsTo(Airplain::class);
	}

	public function service(){
		return $this->belongsTo(Service::class);
	}

}
