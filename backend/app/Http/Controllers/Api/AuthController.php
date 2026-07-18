<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\AuditLog;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request): UserResource
    {
        $credentials = $request->safe()->only('email', 'password');

        $authenticated = $request->hasSession()
            ? Auth::attempt($credentials, $request->boolean('remember'))
            : Auth::once($credentials);

        if (! $authenticated) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if (! $request->user()->isActive()) {
            $this->logoutWebGuard($request, invalidateSession: true);

            throw ValidationException::withMessages([
                'email' => [__('messages.inactive_account')],
            ]);
        }

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }
        $this->audit($request, 'auth.login');

        return new UserResource($request->user()->load('roles.permissions'));
    }

    public function logout(Request $request): JsonResponse
    {
        $this->audit($request, 'auth.logout');

        $token = $request->user()?->currentAccessToken();

        if ($token !== null && method_exists($token, 'delete')) {
            $token->delete();
        }

        $this->logoutWebGuard($request, invalidateSession: true);

        return response()->json(['message' => __('messages.logged_out')]);
    }

    public function user(Request $request): UserResource
    {
        return new UserResource($request->user()->load('roles.permissions'));
    }

    public function forgot(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);
        Password::sendResetLink($data);

        return response()->json(['message' => __('messages.reset_link_sent')]);
    }

    public function reset(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset($data, function ($user, string $password): void {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();

            $user->tokens()->delete();
            event(new PasswordReset($user));
        });

        if ($status !== Password::PasswordReset) {
            throw ValidationException::withMessages(['email' => [__($status)]]);
        }

        return response()->json(['message' => __($status)]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $request->user()->update(['password' => $data['password']]);
        $request->user()->tokens()->delete();
        $this->audit($request, 'auth.password_changed');

        return response()->json(['message' => __('messages.password_changed')]);
    }

    private function logoutWebGuard(Request $request, bool $invalidateSession = false): void
    {
        if (! $request->hasSession()) {
            Auth::guard('web')->forgetUser();

            return;
        }

        Auth::guard('web')->logout();

        if ($invalidateSession) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
    }

    private function audit(Request $request, string $event): void
    {
        AuditLog::create([
            'user_id' => $request->user()?->id,
            'event' => $event,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
