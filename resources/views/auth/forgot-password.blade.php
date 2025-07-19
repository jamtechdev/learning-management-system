<x-guest-layout>
    <div
        class="flex justify-start w-fit h-fit mb-[20px] rounded-xl lg:hidden">
        <img src="{{ asset('/images/logo/logo-1.png') }}"
            alt
            class="w-auto h-20 text-indigo-600 dark:text-indigo-400">
    </div>
    <div class="mb-4 ">
        <h1 class="mb-2 text-4xl font-bold text-gray-950 ">Forgot Password</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">{{
            __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.')
            }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block w-full mt-1" type="email"
                name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-center mt-4">
            <x-primary-button class="w-full justify-center">
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
