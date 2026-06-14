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
        $user = null;
        $mode = 'email';

        if (str_contains($identifier, '@')) {
            $user = User::where('email', $identifier)->first();
            $mode = 'email';
        } else {
            // Clean non-numeric characters for phone numbers
            $phone = preg_replace('/[^0-9]/', '', $identifier);
            if (!empty($phone)) {
                $user = User::where('phone', $phone)->first();
            }
            $mode = 'whatsapp';
        }

        if (!$user) {
            throw ValidationException::withMessages([
                'identifier' => ['Akun dengan Email/Nomor WA tersebut tidak ditemukan.'],
            ]);
        }

        // Generate reset token manually using Laravel's password broker (stores hashed token under email)
        $token = Password::broker()->createToken($user);
        
        if ($mode === 'whatsapp') {
            // Prepare reset link with phone parameter for custom reset form
            $resetLink = url(route('password.reset', ['token' => $token, 'phone' => $user->phone], false));
            $message = "Halo {$user->name}, klik link berikut untuk mereset kata sandi akun PPOB Anda:\n\n" . $resetLink;

            try {
                $response = Http::withHeaders([
                    'Authorization' => env('FONNTE_TOKEN'),
                ])->timeout(5)->post('https://api.fonnte.com/send', [
                    'target' => $user->phone,
                    'message' => $message,
                ]);

                if ($response->failed()) {
                    Log::error('Fonnte API WhatsApp send failed on reset request: ' . $response->body());
                    return back()->withErrors(['identifier' => 'Gagal mengirim pesan WhatsApp via Fonnte.']);
                }
            } catch (\Exception $e) {
                Log::error('Fonnte Timeout: ' . $e->getMessage());
                return back()->withErrors(['identifier' => 'Terjadi kesalahan saat menghubungi layanan WhatsApp (Timeout).']);
            }
        } else {
            // Send standard Laravel reset notification (SMTP email)
            try {
                $user->sendPasswordResetNotification($token);
            } catch (\Exception $e) {
                Log::error('SMTP Timeout: ' . $e->getMessage());
                return back()->withErrors(['identifier' => 'Gagal terhubung ke server Email. Port SMTP mungkin diblokir oleh VPS.']);
            }
        }

        return back()->with('success', 'Link reset kata sandi telah dikirim ke Email / WhatsApp Anda.');
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
