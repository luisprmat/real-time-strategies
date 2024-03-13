<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'user_id',
        'message',
        'private',
    ];

    protected function casts(): array
    {
        return [
            'private' => 'boolean',
        ];
    }

    protected function time(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => $this->created_at->format('g:i:sa'),
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
