@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-[#f7941e] border-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-[12px] shadow-md']) }}>
