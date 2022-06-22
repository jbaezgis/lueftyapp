@props(['active'])

@php
$classes = ($active ?? false)
            ? 'px-3 py-2 rounded-md bg-blue-200 text-blue-800 whitespace-nowrap mr-3 hover:text-blue-900 hover:bg-blue-200 transition'
            : 'px-3 py-2 rounded-md bg-gray-50 text-gray-800 whitespace-nowrap mr-3 hover:text-gray-900 hover:bg-gray-200 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a> 