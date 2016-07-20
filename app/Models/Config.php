<?php

namespace App\Models;

class Config extends Base
{

    protected $fillable = [
        'path',
        'contents',
    ];

    public function project(){
        return $this->belongsTo('App\Models\Project');
    }

    public function servers(){
        return $this->hasManyThrough('App\Models\Server', 'App\Models\Project');
    }
}
