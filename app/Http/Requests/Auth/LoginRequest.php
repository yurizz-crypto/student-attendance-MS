<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Notifications\SecurityAlertNotification;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'identity_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];

        // Require captcha answer when the flag is active
        if (session('captcha_required')) {
            $rules['captcha_answer'] = ['required', 'integer'];
        }

        return $rules;
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Validate CAPTCHA answer when required
        if (session('captcha_required')) {
            $expected = session('captcha_sum');
            if ((int) $this->input('captcha_answer') !== (int) $expected) {
                throw ValidationException::withMessages([
                    'captcha_answer' => 'Incorrect answer. Please try again.',
                ]);
            }
        }

        if (! Auth::attempt($this->only('identity_id', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            $attempts = RateLimiter::attempts($this->throttleKey());

            // Activate captcha after 3 failed attempts
            if ($attempts >= 3) {
                session(['captcha_required' => true]);
                $this->regenerateCaptcha();
                $this->notifyAdminsOfSecurityAlert($attempts);
            }

            throw ValidationException::withMessages([
                'identity_id' => trans('auth.failed'),
            ]);
        }

        // Clear captcha and rate limiter on success
        session()->forget(['captcha_required', 'captcha_sum', 'captcha_question']);
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Generate a new math CAPTCHA and store in session.
     */
    private function regenerateCaptcha(): void
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        session([
            'captcha_question' => "What is {$a} + {$b}?",
            'captcha_sum' => $a + $b,
        ]);
    }

    /**
     * Notify all admin users of repeated failed login attempts (throttled once per hour per key).
     */
    private function notifyAdminsOfSecurityAlert(int $attempts): void
    {
        $cacheKey = 'security_alert_sent:'.$this->throttleKey();

        // Only send once per hour per throttle key to avoid flooding admins
        if (Cache::has($cacheKey)) {
            return;
        }

        Cache::put($cacheKey, true, now()->addHour());

        $admins = User::where('role', 'admin')->get();

        Notification::send($admins, new SecurityAlertNotification(
            identityId: $this->input('identity_id'),
            ipAddress: $this->ip(),
            attemptCount: $attempts,
            attemptedAt: now()->toDateTimeString()
        ));
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'identity_id' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('identity_id')).'|'.$this->ip());
    }
}
