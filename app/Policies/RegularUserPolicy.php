<?php

namespace App\Policies;

use App\RegularUser;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegularUserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the regular user.
     *
     * @param  \App\User  $user
     * @param  \App\RegularUser  $regularUser
     * @return mixed
     */
    public function view(User $user, RegularUser $regularUser)
    {
        return $user->id === $regularUser->id;
    }

    /**
     * Determine whether the user can create regular users.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user, RegularUser $regularUser)
    {
        return $user->id === $regularUser->id;
    }

    /**
     * Determine whether the user can update the regular user.
     *
     * @param  \App\User  $user
     * @param  \App\RegularUser  $regularUser
     * @return mixed
     */
    public function update(User $user, RegularUser $regularUser)
    {
        return $user->id === $regularUser->id;
    }

    /**
     * Determine whether the user can delete the regular user.
     *
     * @param  \App\User  $user
     * @param  \App\RegularUser  $regularUser
     * @return mixed
     */
    public function delete(User $user, RegularUser $regularUser)
    {
        return $user->id === $regularUser->id;
    }
}
