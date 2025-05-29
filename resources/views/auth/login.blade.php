<x-guest-layout>
    <div class="flex items-center justify-center min-h-screen px-4 ">
        <div class="w-full max-w-md p-8 space-y-6 bg-white shadow-xl rounded-2xl">
            <!-- Logo (optional) -->
            <div class="flex justify-center">

                <img src="{{ asset('/images/logo/logo-1.png') }}" alt=""
                    class="w-auto h-10 text-indigo-600 dark:text-indigo-400">
            </div>

            <!-- Heading -->
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900">Welcome Back 👋</h1>
                <p class="mt-2 text-gray-500">Please sign in to your account to continue your journey</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus
                        class="block w-full mt-1" placeholder="you@example.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" type="password" name="password" required class="block w-full mt-1"
                        placeholder="Enter your password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" name="remember"
                            class="text-indigo-600 border-gray-300 rounded shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-indigo-600 hover:underline" href="{{ route('password.request') }}">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <div>
                    <x-primary-button class="justify-center w-full">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>

            <!-- Register CTA -->
            {{-- <div class="text-sm text-center text-gray-600">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-indigo-600 hover:underline">Sign up</a>
            </div> --}}
        </div>
    </div>
</x-guest-layout>
