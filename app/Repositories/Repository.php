<?php namespace App\Repositories;

interface Repository{
	public function getById($id);
	public function getAll();
	public function update($object, $data);
	public function save($data);
}