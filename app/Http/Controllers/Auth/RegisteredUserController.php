<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\roles;
use App\Models\User;
use App\Models\user_roles;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
//        add role to user
        $role = roles::where('role_name', 'User')->first();
        $role?? $role = roles::create([
            'role_name' => 'User',
            'role_description' => 'User role',
            'role_slug' => 'user',
        ]);
        $user_role= user_roles::create([
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);

        event(new Registered($user));

        Auth::login($user);




        return redirect(RouteServiceProvider::HOME);
    }
}
