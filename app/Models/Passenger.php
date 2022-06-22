<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Passenger extends Model {

	protected $table = 'passengers';
	protected $guarded = ['id'];
	public $timestamps = false;

}
