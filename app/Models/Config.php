<?php

namespace App\Models;

final class Config extends Base
{

    protected $validation_rules = [
        'path' => 'required:max:255',
        'contents' => 'required'
    ];

    protected $fillable = [
        'path',
        'contents',
    ];

    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    public function servers()
    {
        return $this->belongsToMany('App\Models\Server');
    }


}
