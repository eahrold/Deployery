<?php

namespace App\Presenters;

/**
 * User presenter class
 */
class Team extends Presenter
{
    public function menuName()
    {
        $name = htmlspecialchars_decode($this->entity->name);
        return $this->entity->isCurrentTeam() ? "<b>{$name}</b>" : $name;
    }

}
