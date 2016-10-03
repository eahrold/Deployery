<?php

namespace App\Presenters;

/**
 * History presenter class
 */
class History extends Presenter
{

    public function created_at()
    {
        return $this->entity->created_at->toDayDateTimeString();
    }

    public function updated_at()
    {
        return $this->entity->created_at->toDayDateTimeString();
    }
}
