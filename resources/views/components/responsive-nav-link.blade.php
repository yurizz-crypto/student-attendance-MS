@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-3 border-l-4 border-brand text-start text-base font-bold text-brand bg-brand/5 focus:outline-none focus:text-brand-hover focus:bg-brand/10 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-3 border-l-4 border-transparent text-start text-base font-medium text-gray-500 hover:text-navy hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-navy focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>