<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationAlias extends Model {
	protected $table = 'location_alias';
	protected $guarded = ['id'];
	public $timestamps = false;
}