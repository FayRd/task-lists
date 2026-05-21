@props(['type' => 'button', 'variant' => 'primary'])

@php
$classes = match($variant) {
    'secondary' => 'px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300 transition',
    'danger'    => 'px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition',
    default     => 'px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition',
};
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>