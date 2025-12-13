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

    public bool $showPassword = false;

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

        if (!$user || !Auth::getProvider()->validateCredentials($user, ['password' => $this->password])) {
            RateLimiter::hit($this->throttleKey());

            // Reset password field on failed login
            $this->password = '';

            throw ValidationException::withMessages([
                'username' => 'Username atau password yang Anda masukkan salah. Silakan coba lagi.',
            ]);
        }

        return $user;
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.',
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->username) . '|' . request()->ip());
    }
}; ?>

<div class="flex flex-col items-center justify-center min-h-screen p-4 bg-neutral-50">
    {{-- Login Card --}}
    <div class="w-full max-w-md">
        {{-- Card Container --}}
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            {{-- Header Section with Logo --}}
            <div class="bg-primary px-8 py-8 text-center">
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('favicon.svg') }}" class="w-16 h-16 animate-pulse" alt="Cerita Cireng Logo">
                </div>
                <h1 class="text-h3 font-bold text-dark">Cerita Cireng</h1>
                <p class="text-1 text-dark/80 mt-1">Sistem Manajemen Terpadu</p>
            </div>

            {{-- Login Form --}}
            <div class="px-8 py-8">

                <form wire:submit="login" class="flex flex-col gap-5">
                    @error('username')
                    <div
                        class="mt-2 bg-secondary/10 border-l-4 border-secondary rounded-r-lg p-3 flex items-start gap-2">
                        <i class="ph ph-warning-circle text-secondary text-xl flex-shrink-0 mt-0.5"></i>
                        <p class="text-secondary text-1 font-medium">{{ $message }}</p>
                    </div>
                    @enderror
                    {{-- Username Input --}}
                    <div>
                        <label for="username" class="block text-reguler font-medium text-dark mb-2">
                            <i class="ph ph-user mr-1"></i> Username
                        </label>
                        <input wire:model="username" type="text" id="username"
                            class="w-full px-4 py-3 border-2 border-neutral-100 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-reguler @error('username') border-secondary @enderror"
                            placeholder="Masukkan username Anda" required autofocus />

                    </div>

                    {{-- Password Input --}}
                    <div>
                        <label for="password" class="block text-reguler font-medium text-dark mb-2">
                            <i class="ph ph-lock mr-1"></i> Password
                        </label>
                        <div class="relative">
                            <input wire:model="password" type="{{ $showPassword ? 'text' : 'password' }}" id="password"
                                class="w-full px-4 py-3 pr-12 border-2 border-neutral-100 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-reguler @error('password') border-secondary @enderror"
                                placeholder="Masukkan password Anda" required />
                            <button type="button" wire:click="$toggle('showPassword')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-dark transition-colors p-1 cursor-pointer"
                                title="{{ $showPassword ? 'Sembunyikan password' : 'Tampilkan password' }}">
                                <i class="ph {{ $showPassword ? 'ph-eye-slash' : 'ph-eye' }} text-xl"></i>
                            </button>
                        </div>
                        @error('password')
                        <div
                            class="mt-2 bg-secondary/10 border-l-4 border-secondary rounded-r-lg p-3 flex items-start gap-2">
                            <i class="ph ph-warning-circle text-secondary text-xl flex-shrink-0 mt-0.5"></i>
                            <p class="text-secondary text-1 font-medium">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center">
                        <input wire:model="remember" type="checkbox" id="remember"
                            class="w-4 h-4 text-primary border-neutral-300 rounded focus:ring-2 focus:ring-primary/20 cursor-pointer" />
                        <label for="remember" class="ml-2 text-1 text-neutral-400 cursor-pointer select-none">
                            Ingat saya di perangkat ini
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full bg-primary text-dark py-3.5 rounded-lg cursor-pointer font-bold text-reguler hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 mt-2 flex items-center justify-center gap-2">
                        <i class="ph ph-sign-in text-xl"></i>
                        <span>Masuk Sekarang</span>
                    </button>
                </form>

            </div>
        </div>

        {{-- Bottom Note --}}
        <div class="text-center mt-6">
            <p class="text-1 text-neutral-400">
                © 2025 - 2026 Cerita Cireng. All rights reserved.
            </p>
        </div>
    </div>
</div>