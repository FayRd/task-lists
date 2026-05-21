<?php

use App\Models\TodoList;
use App\Models\TodoItem;
use Livewire\Component;
use Livewire\Attributes\Computed;

new class extends Component
{
    public TodoList $todoList;
    public string $newItem = '';

    public function mount(TodoList $todoList)
    {
        $this->authorize('view', $todoList);
        $this->todoList = $todoList;
    }

    #[Computed]
    public function items()
    {
        return $this->todoList->todoItems()->orderBy('position')->get();
    }

    public function addItem()
    {
        $this->validate(['newItem' => 'required|string|max:500']);

        $maxPosition = $this->todoList->todoItems()->max('position') ?? 0;

        $this->todoList->todoItems()->create([
            'body'     => $this->newItem,
            'position' => $maxPosition + 1,
        ]);

        $this->newItem = '';
    }

    public function toggleItem(TodoItem $item)
    {
        $this->authorize('update', $this->todoList);
        $item->update(['is_completed' => ! $item->is_completed]);
    }

    public function deleteItem(TodoItem $item)
    {
        $this->authorize('update', $this->todoList);
        $item->delete();
    }
};
?>

<div class="max-w-3xl mx-auto py-8 px-4">
    <div class="mb-6">
        <a href="{{ route('lists.index') }}"
           class="text-sm text-indigo-600 hover:underline">← Back to lists</a>
        <h1 class="text-2xl font-semibold mt-2">{{ $this->todoList->name }}</h1>
        @if($this->todoList->description)
            <p class="text-gray-500 text-sm mt-1">{{ $this->todoList->description }}</p>
        @endif
        <div class="mt-4 flex items-center gap-4">
            @if($this->todoList->listShare && $this->todoList->listShare->expires_at->isFuture())
                <div class="flex items-center gap-2 text-sm">
                    <input type="text"
                        readonly
                        value="{{ route('share.preview', $this->todoList->listShare->token) }}"
                        class="border border-gray-200 rounded px-3 py-1.5 text-xs text-gray-600 w-72 bg-gray-50" />
                    <form method="POST" action="{{ route('lists.share', $this->todoList) }}">
                        @csrf
                        <button type="submit"
                                class="text-xs text-indigo-600 hover:underline">
                            Regenerate
                        </button>
                    </form>
                </div>
            @else
                <form method="POST" action="{{ route('lists.share', $this->todoList) }}">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg text-sm hover:bg-indigo-200">
                        Share as preset
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="flex gap-2 mb-6">
        <input wire:model="newItem"
               wire:keydown.enter="addItem"
               type="text"
               placeholder="Add a new item..."
               class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm" />
        <button wire:click="addItem"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
            Add
        </button>
    </div>
    @error('newItem')
        <p class="text-red-500 text-xs mb-4">{{ $message }}</p>
    @enderror

    <div class="space-y-2">
        @forelse($this->items as $item)
            <div class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg
                        {{ $item->is_completed ? 'opacity-60' : 'opacity-100' }}">
                <input type="checkbox"
                       wire:click="toggleItem({{ $item->id }})"
                       @checked($item->is_completed)
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600" />
                <span class="flex-1 text-sm {{ $item->is_completed ? 'line-through text-gray-400' : '' }}">
                    {{ $item->body }}
                </span>
                <button wire:click="deleteItem({{ $item->id }})"
                        wire:confirm="Remove this item?"
                        class="text-xs text-red-400 hover:text-red-600">
                    Remove
                </button>
            </div>
        @empty
            <p class="text-gray-400 text-center py-8">No items yet. Add one above!</p>
        @endforelse
    </div>
</div>