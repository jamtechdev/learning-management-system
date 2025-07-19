@props(['active'])

@php
$classes = $active
    ? 'inline-flex items-center w-full gap-3 px-4 py-3 font-bold text-black bg-[#faf0e6] rounded-lg  dark:border-indigo-400 dark:text-indigo-400 dark:bg-indigo-800'
    : 'inline-flex items-center w-full gap-3 px-4 font-bold py-3 text-black rounded-lg dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-indigo-400 dark:hover:bg-indigo-800';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
