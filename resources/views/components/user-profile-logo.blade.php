@props(['user' => null])

@php
    $profileImage = $user?->avatar;
    $fallbackImage = asset('images/logo/default-avatar.png');
@endphp

<div x-data="{ showImage: false }" {{ $attributes }} class="relative">
    <!-- Small Profile Image -->
    <img
        src="{{ $profileImage ?? $fallbackImage }}"
        onerror="this.onerror=null;this.src='{{ $fallbackImage }}';"
        alt="{{ $user?->name ?? 'User' }}"
        class="w-8 h-8 border border-gray-300 rounded-full cursor-pointer dark:border-gray-700"
        @click="showImage = true"
    >

    <!-- Fullscreen Modal -->
    <div x-show="showImage"
         @click.away="showImage = false"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70"
    >
        <div class="relative">
            <img
                src="{{ $profileImage ?? $fallbackImage }}"
                onerror="this.onerror=null;this.src='{{ $fallbackImage }}';"
                alt="{{ $user?->name ?? 'User' }}"
                class="max-w-full max-h-screen border border-white rounded shadow-lg"
            >
            <!-- Close Button -->
            <button
                @click="showImage = false"
                class="absolute text-xl font-bold text-white top-2 right-2"
            >&times;</button>
        </div>
    </div>
</div>
