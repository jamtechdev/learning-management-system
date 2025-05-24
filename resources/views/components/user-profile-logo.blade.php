@props(['user' => null])

@php
    $profileImage = $user?->avatar;
    $fallbackImage = asset('images/logo/default-avatar.png');
@endphp

<div {{ $attributes }} class="relative">
    <img src="{{ $profileImage ?? $fallbackImage }}"
         onerror="this.onerror=null;this.src='{{ $fallbackImage }}';"
         alt="{{ $user?->first_name . $user->last_name ?? 'User' }}"
         class="w-8 h-8 border border-gray-300 rounded-full dark:border-gray-700">
</div>
