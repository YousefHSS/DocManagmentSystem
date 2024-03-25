<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\roles;
use App\Models\User;
use App\Models\user_roles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Symfony\Component\ErrorHandler\Error\ClassNotFoundError;


class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function changeRole(Request $request): RedirectResponse
    {
// if user is admin change request user role
        if (auth()->user()->hasRole('admin')) {
            $user = User::find($request->user_id);
            $role = roles::where('role_slug', $request->role)->firstOrFail();
            try {
                $user_role = user_roles::where('user_id', $user->id)->firstOrFail();
                $user_role->role_id = $role->id;
                $user_role->save();
            }
            catch  ( \Exception $e) {

                $user_role = user_roles::create([
                    'user_id' => $user->id,
                    'role_id' => $role->id,
                ]);

            }


        }



        return Redirect::back();
    }
}
