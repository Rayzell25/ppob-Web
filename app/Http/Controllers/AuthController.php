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

        // Send WhatsApp welcome message via Fonnte API
        try {
            $fonnteToken = env('FONNTE_TOKEN');
            $message = "Halo {$user->name}! Selamat datang di Rayzell Store. Akun Anda berhasil didaftarkan dengan email: {$user->email}. Selamat bertransaksi!";
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'target' => $user->phone,
                    'message' => $message,
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $fonnteToken
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Fonnte register notification exception: " . $e->getMessage());
        }

        Auth::login($user);

        return redirect('/');
    }
}
