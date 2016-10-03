<?php
namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class UserPolicy
{
    use HandlesAuthorization;

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

    public function destroy(User $user, Model $model)
    {
        return ($user->id != $model->id);
    }
}
