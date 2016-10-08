<?php

namespace App\Models;

use App\Presenters\PresentableTrait;
use Mpociot\Teamwork\TeamworkTeam;

class Team extends TeamworkTeam
{
    use PresentableTrait;

    protected $presenter = 'App\Presenters\Team';

    public function projects()
    {
        return $this->hasMany('App\Models\Project')->order();
    }

    public function isCurrentTeam()
    {
        return auth()->user()->current_team_id === $this->id;
    }
}
