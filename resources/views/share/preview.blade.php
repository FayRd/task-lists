<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $todoList->name }} — Shared List</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto py-12 px-4">

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
            <p class="text-xs font-medium text-indigo-600 uppercase tracking-wide mb-1">
                Shared List Preview
            </p>
            <h1 class="text-2xl font-semibold mb-1">{{ $todoList->name }}</h1>
            @if($todoList->description)
                <p class="text-gray-500 text-sm">{{ $todoList->description }}</p>
            @endif
            <p class="text-xs text-gray-400 mt-3">
                Link expires {{ $share->expires_at->diffForHumans() }}
            </p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
            <h2 class="text-sm font-medium text-gray-500 mb-4">
                {{ $todoList->todoItems->count() }} items in this list
            </h2>
            <div class="space-y-2">
                @forelse($todoList->todoItems as $item)
                    <div class="flex items-center gap-3 py-2 border-b border-gray-100 last:border-0">
                        <div class="h-4 w-4 rounded border-2 border-gray-300 flex-shrink-0"></div>
                        <span class="text-sm text-gray-700">{{ $item->body }}</span>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm">This list has no items.</p>
                @endforelse
            </div>
        </div>

        @auth
            <form method="POST" action="{{ route('share.import', $share->token) }}">
                @csrf
                <button type="submit"
                        class="w-full py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">
                    Import this list into my account
                </button>
            </form>
        @else
            <a href="{{ route('share.claim', $share->token) }}"
               class="block w-full py-3 bg-indigo-600 text-white rounded-lg font-medium text-center hover:bg-indigo-700 transition">
                Log in to import this list
            </a>
        @endauth

    </div>
</body>
</html>