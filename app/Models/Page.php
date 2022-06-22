<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PageMeta;

class Page extends Model
{
	public $timestamps = false;

    protected $connection = 'wp';
    protected $table = 'posts';
}