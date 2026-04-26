@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-brand focus:ring-brand rounded-xl shadow-sm bg-gray-50 text-navy py-2.5 px-4 transition-colors disabled:opacity-50 disabled:bg-gray-100 sm:text-sm sm:leading-6']) }}>