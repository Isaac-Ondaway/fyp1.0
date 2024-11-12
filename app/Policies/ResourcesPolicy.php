<?php

namespace App\Policies;

use App\Models\User;

class ResourcesPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Resource $resource)
{
    return $user->role === 'admin';
}

}
