<?php

use App\Models\TodoList;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Computed;

new class extends Component
{
    public string $name = '';
    public string $description = '';
    public bool $showForm = false;

    #[Computed]
    public function lists()
    {
        return Auth::user()->todoLists()->withCount('todoItems')->latest()->get();
    }

    public function create()
    {
        $this->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        Auth::user()->todoLists()->create([
            'name'        => $this->name,
            'description' => $this->description,
        ]);

        $this->name        = '';
        $this->description = '';
        $this->showForm    = false;
    }

    public function delete(TodoList $list)
    {
        $this->authorize('delete', $list);
        $list->delete();
    }
};
?>

<div class="max-w-3xl mx-auto py-8 px-4">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">My Lists</h1>
        <button wire:click="$set('showForm', true)"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
            Create List
        </button>
    </div>

    @if($this->showForm)
        <div class="mb-6 p-4 border border-gray-200 rounded-lg bg-gray-700">
            <h2 class="text-lg font-medium mb-4">Create a new list</h2>
            <input wire:model="name"
                   type="text"
                   placeholder="List name"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 mb-3 text-sm" />
            <textarea wire:model="description"
                      placeholder="Description (optional)"
                      class="w-full border border-gray-300 rounded-md px-3 py-2 mb-3 text-sm"
                      rows="2"></textarea>
            @error('name')
                <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
            @enderror
            <div class="flex gap-2">
                <button wire:click="create"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
                    Create
                </button>
                <button wire:click="$set('showForm', false)"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    @endif

    <div class="space-y-3">
        @forelse($this->lists as $list)
            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-indigo-300 transition">
                <a href="{{ route('lists.show', $list) }}" class="flex-1">
                    <p class="font-medium">{{ $list->name }}</p>
                    <p class="text-sm text-gray-500">{{ $list->todo_items_count }} items</p>
                </a>
                <button wire:click="delete({{ $list->id }})"
                        wire:confirm="Delete '{{ $list->name }}'?"
                        class="ml-4 text-sm text-red-500 hover:text-red-700">
                    Delete
                </button>
            </div>
        @empty
            <p class="text-gray-400 text-center py-12">No lists yet. Create your first one!</p>
        @endforelse
    </div>
</div>