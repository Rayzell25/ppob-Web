<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login form view.
     */
    public function showLoginForm()
    {
        // Redirect to home if already logged in
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.login');
    }

    /**
     * Process the authentication request using Email or Phone.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginId = $request->input('login_id');
        $loginType = filter_var($loginId, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $credentials = [
            $loginType => $loginId,
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'login_id' => 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
        ])->onlyInput('login_id');
    }

    /**
     * Show the registration form view.
     */
    public function showRegisterForm()
    {
        // Redirect to home if already logged in
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.register');
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'balance' => 0,
            'role' => 'user',
        ]);

        Auth::login($user);

        return redirect('/');
    }
}
