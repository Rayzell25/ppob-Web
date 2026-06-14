<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Illuminate\Support\Facades\Log;

class SocialLoginController extends Controller
{
    /**
     * Redirect the user to the provider authentication page.
     *
     * @param string $provider
     * @return mixed
     */
    public function redirect(string $provider)
    {
        if (!in_array($provider, ['google', 'telegram'])) {
            abort(404, 'Auth provider not supported.');
        }

        try {
            return Socialite::driver($provider)->redirect();
        } catch (Exception $e) {
            Log::error("Socialite redirect error for [{$provider}]: " . $e->getMessage());
            return redirect('/login')->with('error', "Failed to redirect to {$provider}.");
        }
    }

    /**
     * Obtain the user information from the provider and authenticate.
     *
     * @param string $provider
     * @return RedirectResponse
     */
    public function callback(string $provider)
    {
        if (!in_array($provider, ['google', 'telegram'])) {
            abort(404, 'Auth provider not supported.');
        }

        if ($provider === 'telegram') {
            try {
                $driver = Socialite::driver('telegram');
                $driver->setHttpClient(new \GuzzleHttp\Client(['timeout' => 5]));
                $telegramUser = $driver->user();
                $user = User::updateOrCreate(
                    ['telegram_id' => $telegramUser->getId()],
                    [
                        'name' => $telegramUser->getName() ?? $telegramUser->getNickname() ?? 'Telegram User',
                        'email' => $telegramUser->getId() . '@telegram.rayzell.web.id', // Dummy email mutlak
                        'password' => Hash::make(Str::random(24)),
                        'email_verified_at' => now(),
                    ]
                );
                Auth::login($user);
                return redirect()->intended('/admin'); // Arahkan langsung ke dashboard
            } catch (\Exception $e) {
                Log::error('Telegram Auth Error: ' . $e->getMessage());
                return redirect()->route('login')->withErrors(['error' => 'Sistem Telegram sedang sibuk. Silakan coba lagi.']);
            }
        }

        // Google flow
        try {
            $driver = Socialite::driver('google');
            $driver->setHttpClient(new \GuzzleHttp\Client(['timeout' => 5]));
            $socialUser = $driver->user();
            $user = null;

            // Find by google_id first, then by email
            $user = User::where('google_id', $socialUser->getId())->first();

            if (!$user && $socialUser->getEmail()) {
                $user = User::where('email', $socialUser->getEmail())->first();
            }

            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: 'Google User',
                    'email' => $socialUser->getEmail(),
                    'google_id' => $socialUser->getId(),
                    'avatar_url' => $socialUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)),
                    'role' => 'user',
                    'balance' => 0,
                ]);
            } else {
                $user->update([
                    'google_id' => $socialUser->getId(),
                    'avatar_url' => $socialUser->getAvatar() ?: $user->avatar_url,
                ]);
            }

            if ($user) {
                if ($user->is_banned) {
                    return redirect('/login')->with('error', 'Your account has been suspended.');
                }

                Auth::login($user, true);
                return redirect('/');
            }

            return redirect('/login')->with('error', 'Authentication failed.');
        } catch (Exception $e) {
            Log::error("Socialite callback error for [{$provider}]: " . $e->getMessage());
            return redirect('/login')->with('error', "Authentication failed: " . $e->getMessage());
        }
    }
}
