<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Exception;

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

        try {
            $socialUser = Socialite::driver($provider)->user();

            // Find or create user
            $user = null;

            if ($provider === 'google') {
                // Try finding by google_id, then by email
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
                        'role' => 'MEMBER',
                        'balance' => 0,
                    ]);
                } else {
                    // Update Google ID and Avatar if they were not linked yet
                    $user->update([
                        'google_id' => $socialUser->getId(),
                        'avatar_url' => $socialUser->getAvatar() ?: $user->avatar_url,
                    ]);
                }
            } elseif ($provider === 'telegram') {
                // Telegram provider
                $user = User::where('telegram_id', $socialUser->getId())->first();

                if (!$user) {
                    $user = User::create([
                        'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: 'Telegram User',
                        'telegram_id' => $socialUser->getId(),
                        'telegram_username' => $socialUser->getNickname(),
                        'avatar_url' => $socialUser->getAvatar(),
                        'role' => 'MEMBER',
                        'balance' => 0,
                    ]);
                } else {
                    $user->update([
                        'telegram_username' => $socialUser->getNickname() ?: $user->telegram_username,
                        'avatar_url' => $socialUser->getAvatar() ?: $user->avatar_url,
                    ]);
                }
            }

            if ($user) {
                if ($user->is_banned) {
                    return redirect('/login')->with('error', 'Your account has been suspended.');
                }

                Auth::login($user, true);
                return redirect()->intended('/home');
            }

            return redirect('/login')->with('error', 'Authentication failed.');
        } catch (Exception $e) {
            Log::error("Socialite callback error for [{$provider}]: " . $e->getMessage());
            return redirect('/login')->with('error', "Authentication failed: " . $e->getMessage());
        }
    }
}
