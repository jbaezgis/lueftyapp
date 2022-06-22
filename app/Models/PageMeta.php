<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageMeta extends Model
{
	public $timestamps = false;

    protected $connection = 'wp';
    protected $table = 'postmeta';
}
