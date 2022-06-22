<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model {

	protected $table = 'coupons';
	protected $guarded = ['id'];
 	protected $dates = ['created_at', 'updated_at', 'expiration_date'];
}
