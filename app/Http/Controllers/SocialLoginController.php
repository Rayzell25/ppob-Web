<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class SocialLoginController extends Controller
{
    public function redirect(string $provider)
    {
        if (!in_array($provider, ['google', 'telegram'])) {
            abort(404, 'Auth provider not supported.');
        }

        // TAMPILAN ELEGAN TELEGRAM (MENGGANTIKAN LAYAR PUTIH AMPAS)
        if ($provider === 'telegram') {
            $botUsername = env('TELEGRAM_BOT_USERNAME');
            $callbackUrl = url('/auth/telegram/callback');
            
            $html = <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Telegram | Sistem PPOB</title>
    <style>
        body { background-color: #f3f4f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; max-width: 400px; width: 90%; }
        .logo { width: 90px; margin-bottom: 20px; }
        h2 { color: #1f2937; margin-bottom: 10px; font-size: 24px; font-weight: 600; }
        p { color: #6b7280; margin-bottom: 30px; font-size: 15px; line-height: 1.5; }
        .widget-container { display: flex; justify-content: center; }
    </style>
</head>
<body>
    <div class="card">
        <img src="https://upload.wikimedia.org/wikipedia/commons/8/82/Telegram_logo.svg" alt="Telegram" class="logo">
        <h2>Otorisasi Telegram</h2>
        <p>Silakan klik tombol di bawah untuk masuk ke <b>Sistem PPOB</b>.</p>
        <div class="widget-container">
            <script async src="https://telegram.org/js/telegram-widget.js?22" data-telegram-login="{$botUsername}" data-size="large" data-auth-url="{$callbackUrl}"></script>
        </div>
    </div>
</body>
</html>
HTML;
            return response($html);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider, Request $request)
    {
        if (!in_array($provider, ['google', 'telegram'])) {
            abort(404, 'Auth provider not supported.');
        }

        // LOGIKA BYPASS TELEGRAM ANTI MENTAL
        if ($provider === 'telegram') {
            try {
                $tid = $request->get('id');
                $tname = $request->get('first_name') ?? 'Telegram User';

                if (!$tid) {
                    return redirect('/login')->withErrors(['error' => 'Gagal menerima data otorisasi Telegram.']);
                }

                $user = User::where('telegram_id', $tid)->first();
                
                if (!$user) {
                    $user = new User();
                    $user->name = $tname;
                    $user->email = $tid . '@telegram.rayzell.web.id';
                    $user->password = bcrypt(uniqid());
                    $user->phone = 'TG' . $tid; 
                    $user->telegram_id = $tid;
                    $user->email_verified_at = now();
                    $user->save();
                }

                Auth::login($user);
                return redirect()->intended('/');

            } catch (\Exception $e) {
                // JIKA DATABASE MASIH MENOLAK, TAMPILKAN ERROR ASLINYA
                dd("DATABASE ERROR CRASH: " . $e->getMessage());
            }
        }

        // GOOGLE FLOW
        try {
            $driver = Socialite::driver('google');
            $driver->setHttpClient(new \GuzzleHttp\Client(['timeout' => 5]));
            $socialUser = $driver->user();
            
            $user = User::where('google_id', $socialUser->getId())->orWhere('email', $socialUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => bcrypt(\Illuminate\Support\Str::random(16)),
                    'google_id' => $socialUser->getId(),
                    'email_verified_at' => now(),
                ]);
            } else {
                $user->update(['google_id' => $socialUser->getId()]);
            }

            Auth::login($user);
            return redirect()->intended('/');
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['error' => 'Google Login Error: ' . $e->getMessage()]);
        }
    }
}
