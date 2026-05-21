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
        <x-primary-button wire:click="$set('showForm', true)">
            Create List
        </x-primary-button>
    </div>

    @if($this->showForm)
        <x-form-card title="Create a new list">
            <x-text-input wire:model="name" placeholder="List name" class="mb-3" />
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
                    <x-primary-button wire:click="create">
                        Create
                    </x-primary-button>
                <x-primary-button variant="secondary" wire:click="$set('showForm', false)">
                    Cancel
                </x-primary-button>
                </div>
        </x-form-card>
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