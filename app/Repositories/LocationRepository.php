<?php namespace App\Repositories;

use App\Models\Location;
use Cache;

class LocationRepository {
	public function getAll(){
		$locations = Cache::rememberForever('locations', function(){
			return Location::all();
		});
	}

	public function getByName($name){
		return Location::where('location_name', $name)->first();
	}
	
	public function save($data){
		$this->refreshCache();
		return Location::create($data);
	}
	
	public function update($id, $data){
		$this->refreshCache();
		return Location::where('id', $id)->update($data);
	}
	
	private function refreshCache(){
		Cache::forget('locations');
		Cache::forget('locationsJSON');
	}
}