@props(['title'])

<div class="mb-6 p-4 border border-gray-200 rounded-lg">
    <h2 class="text-lg font-medium mb-4">{{ $title }}</h2>
    {{ $slot }}
</div>