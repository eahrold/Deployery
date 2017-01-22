<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user is the owner or a team member of the project;
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Project  $project
     * @return mixed
     */
    protected function isOwnerOrTeamMember (User $user, Project $project) {
        return ($project->user_id == $user->id) ||
               ($project->team_id == $user->current_team_id);
    }

    public function index(User $user)
    {
        return true;
    }

    public function listChildren(User $user, Project $project)
    {
        return $this->isOwnerOrTeamMember($user, $project);
    }
    /**
     * Determine whether the user can view the project.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Project  $project
     * @return mixed
     */
    public function view(User $user, Project $project)
    {
        return $this->isOwnerOrTeamMember($user, $project);
    }

    /**
     * Determine whether the user can create projects.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the project.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Project  $project
     * @return mixed
     */
    public function update(User $user, Project $project)
    {
        return $this->isOwnerOrTeamMember($user, $project);
    }

    /**
     * Determine whether the user can delete the project.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Project  $project
     * @return mixed
     */
    public function delete(User $user, Project $project)
    {
        return $this->isOwnerOrTeamMember($user, $project);
    }

    /**
     * Determine whether the user can show the project.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Project  $project
     * @return mixed
     */
    public function deploy(User $user, Project $project)
    {
        return $this->isOwnerOrTeamMember($user, $project);
    }
}
