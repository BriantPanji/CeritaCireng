<?php

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string')]
    public string $username = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        $user = $this->validateCredentials();

        Auth::login($user, $this->remember);

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Validate the user's credentials.
     */
    protected function validateCredentials(): User
    {
        $user = Auth::getProvider()->retrieveByCredentials(['username' => $this->username, 'password' => $this->password]);

        if (! $user || ! Auth::getProvider()->validateCredentials($user, ['password' => $this->password])) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        return $user;
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->username).'|'.request()->ip());
    }
}; ?>

<div class="flex flex-col items-center justify-center min-h-screen p-4">
    {{-- Login Card --}}
    <div class="w-full max-w-md">
        {{-- Header --}}
        <div class="bg-primary rounded-t-lg px-6 py-4 text-center">
            <h1 class="text-l2 font-bold text-dark">Cerita Cireng</h1>
            <p class="text-reguler text-dark mt-1">Sistem Internal Perusahaan</p>
        </div>

        {{-- Login Form --}}
        <div class="bg-white shadow-reguler rounded-b-lg px-6 py-8">
            <h2 class="text-l1 font-semibold text-dark mb-6 text-center">Login</h2>

            <form wire:submit="login" class="flex flex-col gap-4">
                {{-- Username Input --}}
                <div>
                    <label for="username" class="block text-reguler font-medium text-dark mb-2">Username</label>
                    <input 
                        wire:model="username"
                        type="text" 
                        id="username"
                        class="w-full px-4 py-3 border-2 border-neutral-100 rounded-lg focus:outline-none focus:border-primary transition-colors text-reguler"
                        placeholder="Masukkan username"
                        required
                        autofocus
                    />
                    @error('username')
                        <p class="text-secondary text-1 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Input --}}
                <div>
                    <label for="password" class="block text-reguler font-medium text-dark mb-2">Password</label>
                    <input 
                        wire:model="password"
                        type="password" 
                        id="password"
                        class="w-full px-4 py-3 border-2 border-neutral-100 rounded-lg focus:outline-none focus:border-primary transition-colors text-reguler"
                        placeholder="Masukkan password"
                        required
                    />
                    @error('password')
                        <p class="text-secondary text-1 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center">
                    <input 
                        wire:model="remember"
                        type="checkbox" 
                        id="remember"
                        class="w-4 h-4 text-primary border-neutral-200 rounded focus:ring-primary"
                    />
                    <label for="remember" class="ml-2 text-1 text-neutral-400">Ingat saya</label>
                </div>

                {{-- Submit Button --}}
                <button 
                    type="submit"
                    class="w-full bg-primary text-white py-3 rounded-lg font-semibold text-reguler hover:bg-primary-200 transition-colors mt-2"
                >
                    Login
                </button>
            </form>
        </div>
    </div>
</div>
