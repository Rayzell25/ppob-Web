<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class WAAuthController extends Controller
{
    /**
     * Show the forgot password form.
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link via WhatsApp Fonnte API or SMTP Email.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
        ]);

        $identifier = trim($request->identifier);
        $user = User::where('email', $identifier)->orWhere('phone', $identifier)->first();

        // Fallback to strip non-numeric characters if phone search failed
        if (!$user && !str_contains($identifier, '@')) {
            $cleanedPhone = preg_replace('/[^0-9]/', '', $identifier);
            if (!empty($cleanedPhone)) {
                $user = User::where('phone', $cleanedPhone)->first();
            }
        }

        if (!$user) {
            return back()->withErrors(['identifier' => 'Akun dengan Email/Nomor WA tersebut tidak ditemukan.']);
        }

        $token = Password::broker()->createToken($user);
        $resetLink = url(route('password.reset', ['token' => $token, 'email' => $user->email], false));

        if (str_contains($identifier, '@')) {
            // Mode Email: Eksekusi dalam Try-Catch
            try {
                $user->sendPasswordResetNotification($token);
            } catch (\Exception $e) {
                Log::error('SMTP Reset Error: ' . $e->getMessage());
                // Tetap lanjut agar UI sukses, biarkan error tercatat di log
            }
        } else {
            // Mode WhatsApp: Fonnte API dengan Strict Timeout 3s
            try {
                Http::timeout(3)
                    ->withHeaders(['Authorization' => env('FONNTE_TOKEN')])
                    ->post('https://api.fonnte.com/send', [
                        'target' => $user->phone ?? $identifier,
                        'message' => "Halo {$user->name},\n\nKlik link berikut untuk mereset kata sandi akun PPOB Anda:\n{$resetLink}\n\nLink ini kedaluwarsa dalam 60 menit."
                    ]);
            } catch (\Exception $e) {
                Log::error('Fonnte Reset Error: ' . $e->getMessage());
            }
        }

        return back()
            ->with('status', 'Jika akun valid, link reset telah dikirim ke Email / WhatsApp Anda.')
            ->with('success', 'Jika akun valid, link reset telah dikirim ke Email / WhatsApp Anda.');
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'phone' => $request->phone,
            'email' => $request->email,
        ]);
    }

    /**
     * Process password reset.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $identifier = $request->phone ?? $request->email;
        if (!$identifier) {
            return back()->withErrors(['password' => 'Email atau nomor telepon tidak ditemukan.']);
        }

        // Find the user by phone or email
        $user = User::where('phone', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        if (!$user) {
            return back()->withErrors(['password' => 'Akun tidak ditemukan.']);
        }

        // Validate token using standard Laravel Password Broker
        $isValid = Password::broker()->tokenExists($user, $request->token);

        if (!$isValid) {
            // Check if there is a legacy plain-text token stored under phone/email
            $legacyReset = DB::table('password_reset_tokens')
                ->where('email', $identifier)
                ->where('token', $request->token)
                ->first();

            if (!$legacyReset) {
                return back()->withErrors(['password' => 'Token reset password tidak valid atau telah kedaluwarsa.']);
            }
        }

        // Update user's password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete the token
        Password::broker()->deleteToken($user);
        DB::table('password_reset_tokens')->where('email', $identifier)->delete();

        return redirect('/login')->with('success', 'Password Anda berhasil diperbarui! Silakan masuk menggunakan password baru.');
    }
}
