<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Interview;
use App\Models\User;

class InterviewPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Interview $interview): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Interview $interview)
    {
        // Admins can update any interview
        if ($user->hasRole('admin')) {
            return true;
        }

        // Faculty can update interviews only for their own programs
        return $user->id === $interview->program->facultyID;
    }


    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Interview $interview): bool
    {
        return $user->hasRole('admin') || $user->id === $interview->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Interview $interview): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Interview $interview): bool
    {
        //
    }
}
