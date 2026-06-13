<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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
     * Send password reset link via WhatsApp Fonnte API.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|exists:users,phone',
        ]);

        $token = Str::random(60);

        // Save token to password_reset_tokens (using phone in the email primary key column)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->phone],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );

        $resetUrl = url("/reset-password/{$token}?phone=" . urlencode($request->phone));
        $message = "Halo! Seseorang meminta reset password untuk akun Rayzell Store Anda. Abaikan jika ini bukan Anda. Klik link berikut untuk membuat password baru: " . $resetUrl;

        // Send WhatsApp message via Fonnte API
        try {
            $fonnteToken = env('FONNTE_TOKEN');
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
                    'target' => $request->phone,
                    'message' => $message,
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $fonnteToken
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                Log::error("Fonnte cURL Error: " . $err);
                return back()->withErrors(['phone' => 'Gagal mengirim pesan WhatsApp. Silakan coba beberapa saat lagi.']);
            }

            Log::info("Fonnte Response: " . $response);
        } catch (\Exception $e) {
            Log::error("Fonnte Exception: " . $e->getMessage());
            return back()->withErrors(['phone' => 'Gagal mengirim pesan WhatsApp: ' . $e->getMessage()]);
        }

        return back()->with('success', 'Link reset password telah dikirim ke nomor WhatsApp Anda.');
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'phone' => $request->phone,
        ]);
    }

    /**
     * Process password reset.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'phone' => 'required|string|exists:users,phone',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->phone)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return back()->withErrors(['phone' => 'Token reset password tidak valid atau telah kedaluwarsa.']);
        }

        // Update user's password
        $user = User::where('phone', $request->phone)->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $request->phone)->delete();

        return redirect('/login')->with('success', 'Password Anda berhasil diperbarui! Silakan masuk menggunakan password baru.');
    }
}
