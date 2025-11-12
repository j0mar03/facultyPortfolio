<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create Account</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Register to create your faculty portfolio</p>
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="{{ __('Full Name') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" placeholder="Juan Dela Cruz" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email Address') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" placeholder="your.email@pup.edu.ph" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" placeholder="Minimum 8 characters" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" placeholder="Re-enter your password" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-start">
                            <x-checkbox name="terms" id="terms" class="mt-1" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="text-sm text-maroon-600 hover:text-maroon-800 dark:text-maroon-400 dark:hover:text-maroon-300 font-medium underline">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="text-sm text-maroon-600 hover:text-maroon-800 dark:text-maroon-400 dark:hover:text-maroon-300 font-medium underline">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="mt-6">
                <x-button class="w-full justify-center bg-maroon-800 hover:bg-maroon-900 focus:bg-maroon-900 active:bg-maroon-950">
                    {{ __('Create Account') }}
                </x-button>
            </div>

            <div class="mt-4 text-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Already have an account? </span>
                <a href="{{ route('login') }}" class="text-sm font-medium text-maroon-600 hover:text-maroon-800 dark:text-maroon-400 dark:hover:text-maroon-300">
                    Sign in here
                </a>
            </div>
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
