<?php

namespace App;
use App\Models\Location;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    
}
