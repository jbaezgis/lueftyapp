<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['tour_id', 'name', 'email', 'phone', 'persons', 'date', 'hotel', 'room_number', 'total'];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
