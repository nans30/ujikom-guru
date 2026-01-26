<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class SocialLoginController extends Controller
{
    public function __construct()
    {
        $settings = Setting::first()->values['social_login'] ?? [];
        foreach (['google', 'facebook'] as $provider) {
            if (isset($settings[$provider]) && is_array($settings[$provider])) {
                config([
                    "services.$provider.client_id" => $settings[$provider]["{$provider}_client_id"] ?? '',
                    "services.$provider.client_secret" => $settings[$provider]["{$provider}_client_secret"] ?? '',
                    "services.$provider.redirect" => $settings[$provider]["{$provider}_redirect_url"] ?? '',
                ]);
            }
        }
    }

    public function redirectToProvider($provider)
    {
        $validProviders = ['google', 'facebook'];

        if (!in_array($provider, $validProviders)) {
            return response()->json(['error' => 'Invalid provider'], 400);
        }

        $googleCredentials = Setting::first()->values['social_login'][$provider] ?? null;
        $clientId = $googleCredentials[$provider . '_client_id'] ?? null;
        $clientSecret = $googleCredentials[$provider . '_client_secret'] ?? null;
        $redirectUrl = $googleCredentials[$provider . '_redirect_url'] ?? null;

        if (!$redirectUrl || !$clientId || !$clientSecret) {
            return redirect()->back()->with('error', ucfirst($provider) . ' credentials are not properly set in the settings.');
        }

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();

            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $nameParts = explode(" ", $socialUser->getName(), 2);
                $user = User::create([
                    'first_name' => $nameParts[0] ?? '',
                    'last_name' => $nameParts[1] ?? '',
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(123456789),
                    'status' => true,
                ]);

                $role = Role::where('name', RoleEnum::USER)->first();
                if ($role) {
                    $user->assignRole($role);
                }
            }

            Auth::login($user);
            return redirect()->route('admin.dashboard');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Social login failed: ' . $e->getMessage());
        }
    }
}
