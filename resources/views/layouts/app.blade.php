<x-layouts::app.sidebar :title="$title ?? null">
    @if(session('status'))
        <div class="max-w-3xl mx-auto px-4 pt-4">
            <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3">
                {{ session('status') }}
            </div>
        </div>
    @endif
    <flux:main>
        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>
