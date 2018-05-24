<?php
namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy extends BasePolicy
{
    public function show(User $user, Team $team){
        return $user->isMemberOfTeam($team);
    }

    public function update(User $user, Team $team)
    {
        return $user->isOwnerOfTeam($team);
    }

    public function edit(User $user, Team $team)
    {
        return $user->isOwnerOfTeam($team);
    }

    public function delete(User $user, Team $team)
    {
        return $user->isOwnerOfTeam($team);
    }

    public function destroy(User $user, Team $team)
    {
        return $this->delete($user, $team);
    }

    public function manageTeams(User $user)
    {
        return $user->is_admin || $user->can_manage_teams;
    }

    public function joinTeams(User $user)
    {
        return $user->can_join_teams || $this->manageTeams($user);
    }

    public function switchToTeam(User $user, Team $team)
    {
        return ($user->current_team_id != $team->id) && $user->isTeamMember($team);
    }

    public function invite(User $user, Team $team)
    {
        return $user->isOwnerOfTeam($team) || $user->can_manage_teams;
    }

    public function leave(User $user, Team $team)
    {
        return $user->isTeamMember($team);
    }

    public function join(User $user, Team $team)
    {
        return $user->can_join_teams && !$user->isTeamMember($team);
    }

}
