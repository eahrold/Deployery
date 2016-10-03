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
        'name',
        'user_name',
        'from_commit',
        'to_commit',
    ];

    protected $casts = [
        'success' => 'boolean',
    ];

    // We have both a project, and server relationship here
    // in the case that a server is removed we still want to
    // keep the history attached to something.
    public function project()
    {
        return $this->belongsTo('App\Models\Project')->order();
    }

    public function server()
    {
        return $this->belongsTo('App\Models\Server')->order();
    }

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
            function ($history) {
                event(new HistoryCreatedEvent($history));
            }
        );
    }
}
