<?php namespace App\Repositories;
use App\Repositories\Repository;
use App\Models\Service;

class ServiceRepository implements Repository
{
	public function getById($service_id){
		return Service::findOrFail($service_id);
	}

	public function getAll(){

	}

	public function update($object, $data){

	}

	public function save($data){

	}
}