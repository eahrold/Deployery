<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Base
{

    protected $fillable = [
        'name',
        'user',
        'from_commit',
        'to_commit',
    ];

    // We have both a project, and server relationship here
    // in the case that a server is removed we still want to
    // keep the history attached to something.
    public function project(){
        return $this->belongsTo('App\Models\Project');
    }

    public function server(){
        return $this->hasOne('App\Models\Server');
    }
}
