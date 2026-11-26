<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * use the slug in urls: /boards/g
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * a board can only be deleted when it has no threads left
     */
    public function canDelete(): bool
    {
        return $this->threads()->doesntExist();
    }
}
