<?php

namespace App\Presenters;

/**
 * User presenter class
 */
class User extends Presenter
{
    public function shouldShowTeamMenu()
    {
        return $this->entity->can('manageTeams', $this->entity) ||
               $this->entity->teams->count();
    }

}
