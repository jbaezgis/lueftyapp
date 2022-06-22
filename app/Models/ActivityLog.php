<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_log';

    protected $fillable = ['user_id', 'type', 'module', 'action', 'message', 'user_agent', 'ip'];

    public function user(){
    	return $this->belongsTo('App\User');
    }
}
