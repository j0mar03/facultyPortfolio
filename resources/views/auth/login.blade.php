<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome Back</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Sign in to access your portfolio</p>
        </div>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email Address') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" placeholder="your.email@pup.edu.ph" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" placeholder="Enter your password" required autocomplete="current-password" />
            </div>

            <div class="flex items-center justify-between mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-maroon-600 hover:text-maroon-800 dark:text-maroon-400 dark:hover:text-maroon-300 font-medium" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <div class="mt-6">
                <x-button class="w-full justify-center bg-maroon-800 hover:bg-maroon-900 focus:bg-maroon-900 active:bg-maroon-950">
                    {{ __('Sign in') }}
                </x-button>
            </div>

            @if (Route::has('register'))
                <div class="mt-4 text-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Don't have an account? </span>
                    <a href="{{ route('register') }}" class="text-sm font-medium text-maroon-600 hover:text-maroon-800 dark:text-maroon-400 dark:hover:text-maroon-300">
                        Register here
                    </a>
                </div>
            @endif
        </form>
    </x-authentication-card>

    <style>
        .text-maroon-600 { color: #800000; }
        .hover\:text-maroon-800:hover { color: #5C0000; }
        .dark .dark\:text-maroon-400 { color: #A52A2A; }
        .dark .dark\:hover\:text-maroon-300:hover { color: #CD5C5C; }
        .bg-maroon-800 { background-color: #800000; }
        .hover\:bg-maroon-900:hover { background-color: #5C0000; }
        .focus\:bg-maroon-900:focus { background-color: #5C0000; }
        .active\:bg-maroon-950:active { background-color: #4B0000; }
    </style>
</x-guest-layout>
