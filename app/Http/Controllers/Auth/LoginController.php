<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * API Login - for mobile app (Sanctum token-based)
     */
    public function apiLogin(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!\Illuminate\Support\Facades\Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * API Logout - for mobile app
     * currentAccessToken() is null when authenticated via session (no Bearer token)
     */
    public function apiLogout(\Illuminate\Http\Request $request)
    {
        $user = $request->user();
        $token = $user->currentAccessToken();
        if ($token) {
            $token->delete();
        }
        return response()->json(['success' => true, 'message' => 'Logged out']);
    }

    /**
     * API Update FCM Token - deprecated (PWA uses Web Push now).
     * Kept for backward compatibility, no-op.
     */
    public function updateFcmToken(\Illuminate\Http\Request $request)
    {
        return response()->json(['success' => true, 'message' => 'Use update-push-subscription for web push']);
    }

    /**
     * API Update Push Subscription - for web push notifications (PWA)
     * Receives subscription from navigator.serviceWorker.pushManager.subscribe()
     */
    public function updatePushSubscription(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string|max:500',
            'keys' => 'required|array',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
            'contentEncoding' => 'nullable|string|in:aes128gcm,aesgcm',
        ]);

        $user = $request->user();
        $keys = $request->input('keys');
        $contentEncoding = $request->input('contentEncoding', 'aesgcm');

        $user->updatePushSubscription(
            $request->input('endpoint'),
            $keys['p256dh'] ?? null,
            $keys['auth'] ?? null,
            $contentEncoding
        );

        return response()->json(['success' => true, 'message' => 'Push subscription updated']);
    }
}
