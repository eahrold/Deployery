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
        $restrict = [
            'delete',
            'modifyAdminAttributes',
        ];

        if ($user->is_admin && !in_array($ability, $restrict)) {
            return true;
        }
    }

    public function delete(User $user, User $model)
    {
        return ($this->adminModifyingOther($user, $model) ||
               !$this->nonAdminModifyingSelf($user, $model)) &&
               !$this->adminModifyingSelf($user, $model);
    }

    public function update(User $user, User $model)
    {
        return $this->isSelf($user, $model);
    }

    public function show(User $user, User $model)
    {
        return $this->isSelf($user, $model);
    }

    public function modifyAdminAttributes(User $user, User $model)
    {
        return $user->is_admin && !$this->isSelf($user, $model);
    }

    public function manage(User $user, User $model) {
        return $this->modifyAdminAttributes($user, $model);
    }

    //----------------------------------------------------------
    // Private Helpers
    //-------------------------------------------------------


    private function isSelf(User $user, User $model)
    {
        return ($user->id === $model->id);
    }

    private function adminModifyingOther(User $user, User $model) {
        return $user->is_admin &&  !$this->isSelf($user, $model);
    }

    private function adminModifyingSelf(User $user, User $model)
    {
        return $user->is_admin && $this->isSelf($user, $model);
    }

    private function nonAdminModifyingSelf(User $user, User $model)
    {
        return !$user->is_admin && $this->isSelf($user, $model);
    }
}
