<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\RegistrationToken;
use App\Models\User;
use App\Models\Role;


class RegisterTokenController extends Controller
{
    public function generateStandaloneToken()
    {
        if (!auth()->user()->hasRole('admin')) {
            return redirect('/')->with('error', 'You do not have admin access.');
        }

        $token = Str::random(32);
        $expiration = Carbon::now()->addHours(24);

        RegistrationToken::create([
            'token' => $token,
            'expires_at' => $expiration,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.user.list')->with('success', 'Registration token generated: ' . url('/register/' . $token));
    }


    public function listUsers(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            return redirect('/')->with('error', 'You do not have admin access.');
        }
    
        $roleID = $request->input('roleID'); // Get the current role filter from the request
        $roles = Role::all(); // Fetch all roles
    
        // Fetch users with roles and apply the filter if a role is selected
        $users = User::with('roles')
            ->when($roleID, function ($query, $roleID) {
                $query->whereHas('roles', function ($q) use ($roleID) {
                    $q->where('rolesID', $roleID); // Use the correct column name for roles
                });
            })
            ->get();
    
        return view('admin.user-list', compact('users', 'roles', 'roleID'));
    }
    

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $allRoles = Role::all(); // Fetch all roles for the dropdown
        return view('admin.edit-user', compact('user', 'allRoles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'roles' => 'required|exists:roles,rolesID',
        ]);
    
        // Update user details
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->save();
    
        // Sync roles in the role_user table
        $user->roles()->sync([$request->input('roles')]); // This is the correct place for this line
    
        return redirect()->route('admin.user.list')->with('success', 'User updated successfully.');
    }
    

    
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.user.list')->with('success', 'User deleted successfully.');
    }


    public function showRegistrationForm($token)
    {
        $registrationToken = RegistrationToken::where('token', $token)
            ->where('expires_at', '>', Carbon::now())
            ->where('used', false)
            ->first();

        if (!$registrationToken) {
            return redirect('/')->withErrors('Invalid or expired registration link.');
        }

        return view('auth.register', compact('token'));
    }

    public function registerUser(Request $request, $token)
    {
        $registrationToken = RegistrationToken::where('token', $token)
            ->where('expires_at', '>', Carbon::now())
            ->where('used', false)
            ->first();
    
        if (!$registrationToken) {
            return redirect('/')->withErrors('Invalid or expired registration link.');
        }
    
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);
    
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);
    
        // Assign the "Faculty" role to the new user
        $facultyRole = Role::where('type', 'Faculty')->first();
        if ($facultyRole) {
            $user->roles()->attach($facultyRole->rolesID);
        }
        // Mark the token as used
        $registrationToken->update(['used' => true]);
    
        return redirect('/login')->with('success', 'Registration successful. You can now log in.');
    }


    
}
