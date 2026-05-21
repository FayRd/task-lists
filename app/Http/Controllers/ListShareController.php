<?php

namespace App\Http\Controllers;

use App\Models\TodoList;
use App\Models\ListShare;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ListShareController extends Controller
{
    use AuthorizesRequests;

    public function generate(Request $request, TodoList $todoList)
    {
        $this->authorize('update', $todoList);

        // Mark the list as a preset
        $todoList->update(['is_preset' => true]);

        // Delete any existing share token for this list
        $todoList->listShare()->delete();

        // Create a fresh token expiring in 7 days
        $todoList->listShare()->create([
            'token'      => Str::random(48),
            'expires_at' => now()->addDays(7),
        ]);

        return back()->with('status', 'Share link generated!');
    }

    public function preview(string $token)
    {
        $share = ListShare::where('token', $token)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $todoList = $share->todoList()->with('todoItems')->firstOrFail();

        return view('share.preview', compact('todoList', 'share'));
    }

    public function import(string $token)
    {
        $share = ListShare::where('token', $token)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $original = $share->todoList()->with('todoItems')->firstOrFail();

        // Clone the list into the authenticated user's account
        $newList = auth()->user()->todoLists()->create([
            'name'        => $original->name . ' (imported)',
            'description' => $original->description,
            'is_preset'   => false,
        ]);

        // Clone each item preserving position
        foreach ($original->todoItems as $item) {
            $newList->todoItems()->create([
                'body'         => $item->body,
                'position'     => $item->position,
                'is_completed' => false,
            ]);
        }

        return redirect()->route('lists.show', $newList)
            ->with('status', 'List imported successfully!');
    }

    public function claim(string $token)
    {
        $share = ListShare::where('token', $token)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $original = $share->todoList()->with('todoItems')->firstOrFail();
        
        // Clone the list into the authenticated user's account
        $newList = auth()->user()->todoLists()->create([
            'name'        => $original->name . ' (imported)',
            'description' => $original->description,
            'is_preset'   => false,
        ]);

        // Clone each item preserving position
        foreach ($original->todoItems as $item) {
            $newList->todoItems()->create([
                'body'         => $item->body,
                'position'     => $item->position,
                'is_completed' => false,
            ]);
        }

        return redirect()->route('lists.show', $newList)
            ->with('status', 'List imported successfully!');
    }
}
