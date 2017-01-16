<?php

namespace App\Models;

use App\Models\Traits\Slackable;
use App\Presenters\PresentableTrait;
use Mpociot\Teamwork\TeamworkTeam;

class Team extends TeamworkTeam
{
    use PresentableTrait;
    use Slackable;

    protected $presenter = 'App\Presenters\Team';
    protected $fillable = [
        'name',
        'owner_id',
        'slack_webhook_url',
        'send_slack_messages',
    ];

    public function projects()
    {
        return $this->hasMany('App\Models\Project')->order();
    }

    public function isCurrentTeam()
    {
        return auth()->user()->current_team_id === $this->id;
    }

}
