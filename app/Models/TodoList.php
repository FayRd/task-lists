<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TodoList extends Model
{
    protected $fillable = ['user_id', 'name', 'description', 'is_preset'];

    protected $casts = [
        'is_preset' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function todoItems(): HasMany
    {
        return $this->hasMany(TodoItem::class)->orderBy('position');
    }

    public function listShare(): HasOne
    {
        return $this->HasOne(ListShare::class);
    }
}
