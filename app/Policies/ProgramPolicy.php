<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProgramPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any programs.
     */
    public function viewAny(User $user)
    {
        // Admins can view all programs
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Faculty members can view their own programs
        return $user->hasRole('Faculty');
    }

    /**
     * Retrieve programs based on user role.
     */
    
    public function retrievePrograms(User $user)
    {
        if ($user->hasRole('Admin')) {
            // Admins can view all programs, ordered by faculty name and created_at
            return Program::with('faculty')
                ->join('users', 'programs.facultyID', '=', 'users.id')
                ->orderBy('users.name')  // Order by faculty name
                ->orderBy('programs.created_at')  // Order by creation time
                ->select('programs.*')  // Select all columns from programs
                ->get();
        } else {
            // Non-admin users (e.g., faculty) can only view their own programs, ordered by creation time
            return Program::with('faculty')
                ->where('facultyID', $user->id)
                ->orderBy('programs.created_at')  // Order by creation time
                ->get();
        }
    }

    /**
     * Determine whether the user can view the program.
     */
    public function view(User $user, Program $program)
    {
        // Admins can view all programs, faculty can only view their own
        return $user->hasRole('admin') || $user->id === $program->facultyID;
    }

    /**
     * Determine whether the user can create programs.
     */
    public function create(User $user)
    {
        // Only faculty members can create programs
        return $user->hasRole('faculty');
    }

    /**
     * Determine whether the user can update the program.
     */
    public function update(User $user, Program $program)
    {
        // Admins can update any program, faculty can only update their own
        return $user->hasRole('admin') || $user->id === $program->facultyID;
    }

    /**
     * Determine whether the user can delete the program.
     */
    public function delete(User $user, Program $program)
    {
        // Only the owner of the program can delete it
        return $user->id === $program->facultyID;
    }
}

