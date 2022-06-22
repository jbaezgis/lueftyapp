<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model {

	protected $table = 'reviews';
	protected $guarded = ['id', 'approved'];

	public function getApprovedAttribute($value){
		return $value == 1 ? 'Yes' : 'No';
	}

	public function setApprovedAttribute($value){
		$this->attributes['approved'] = $value == 'Yes' ? '1' : '0';
	}
}
