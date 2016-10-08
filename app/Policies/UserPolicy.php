<?php
namespace App\Policies;

use App\Models\User;

class UserPolicy extends BasePolicy
{
    /**
     * Handel checks that happen before anything else
     * @param  App\Model\User $user  the user to check
     * @param  string $ability abaility to consider
     * @return null|bool true if the the policy should be skipped null otherwise;
     */
    public function before($user, $ability)
    {
        if ($user->is_admin && $ability != 'destroy') {
            return true;
        }
    }

    public function destroy(User $user, User $model)
    {
        return ($this->adminDestroyingOther($user, $model) ||
               !$this->nonAdminDestroyingSelf($user, $model)) &&
               !$this->adminDestroyingSelf($user, $model);
    }

    public function update(User $user, User $model)
    {
        return $this->isSelf($user, $model);
    }

    public function show(User $user, User $model)
    {
        return $this->isSelf($user, $model);
    }

    //----------------------------------------------------------
    // Private Helpers
    //-------------------------------------------------------
    private function isSelf(User $user, Model $model)
    {
        return ($user->id === $model->id);
    }

    private function adminDestroyingOther(User $user, Model $model) {
        return $user->is_admin &&  !$this->isSelf($user, $model);
    }

    private function adminDestroyingSelf(User $user, Model $model)
    {
        return $user->is_admin && $this->isSelf($user, $model);
    }

    private function nonAdminDestroyingSelf(User $user, Model $model)
    {
        return !$user->is_admin && $this->isSelf($user, $model);
    }
}
