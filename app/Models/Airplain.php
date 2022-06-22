<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Airplain extends Model {

	protected $table = 'airplains';
	protected $guarded = ['id'];
	public $timestamps = false;

	// public function airplainprice(){
	// 	return $this->belongsTo('App\Models\AirplainPrice');
	// }

}
