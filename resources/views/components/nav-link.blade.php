@props(['active'])

@php
$classes = $active
    ? 'inline-flex items-center w-full gap-3 px-4 py-3 border-b-2 border-indigo-600 text-gray-900 bg-indigo-100 rounded-lg dark:border-indigo-400 dark:text-indigo-400 dark:bg-indigo-800'
    : 'inline-flex items-center w-full gap-3 px-4 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-indigo-600 hover:bg-indigo-100 rounded-lg dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-indigo-400 dark:hover:bg-indigo-800';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
