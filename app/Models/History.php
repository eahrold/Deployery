<?php

namespace App\Models;

use App\Events\HistoryCreatedEvent;
use App\Presenters\PresentableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

final class History extends Base
{

    use PresentableTrait;

    protected $presenter = 'App\Presenters\History';

    protected $fillable = [
        'user_name',
        'from_commit',
        'to_commit',
        'details',
    ];

    protected $casts = [
        'success' => 'boolean',
        'details' => 'json'
    ];

    protected $hidden = [
        'details'
    ];

    /**
     * We have both a project, and server relationship here
     * in the case that a server is removed we still want to
     * keep the history attached to something.
     *
     * @return \App\Models\Project
     */
    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class);
    }

    /**
     * Server Relationship
     *
     * @return \App\Models\Server
     */
    public function server()
    {
        return $this->belongsTo(\App\Models\Server::class);
    }

    /**
     * Order of the History
     * @param  Mixed|Builder $query Query Builder
     *
     * @return Mixed|Builder $query Query Builder
     */
    public function scopeOrder($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    //----------------------------------------------------------
    // Booting
    //-------------------------------------------------------
    protected static function boot()
    {
        parent::boot();

        static::created(
            function($history) {
                event(new HistoryCreatedEvent($history));
            }
        );
    }
}
