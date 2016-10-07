<?php

namespace App\Models;

use Mpociot\Teamwork\TeamworkTeam;

class Team extends TeamworkTeam
{
    public function projects()
    {
        return $this->hasMany('App\Models\Project')->order();
    }
}