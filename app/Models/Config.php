<?php

namespace App\Models;

final class Config extends Base
{

    protected $fillable = [
        'path',
        'contents',
    ];

    protected $hidden = [
        'server_ids',
    ];

    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    public function servers()
    {
        return $this->belongsToMany('App\Models\Server')->order();
    }

    public function getServersAttirbute()
    {
        if ($ids = $this->server_ids) {
            $whereIns = explode($ids);
            return $this->projects()->servers()->whereIn('id', $whereIns);
        }
        return collect([]);
    }
}
