<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceOption extends Model {
	protected $table = 'price_options';

	function servicePrice(){
		return $this->hasMany('App\Models\ServicePrice');
	}
}
