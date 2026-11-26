<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Thread extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'bumped_at' => 'datetime',
        ];
    }


    /**
     * Check if the thread is owned by the given user. To show (You) on the page.
     *
     * @param User $user
     * @return bool
     */
    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * Check if the user can delete the thread.
     *
     * @param User $user
     * @return bool
     */
    public function canDelete(User $user): bool
    {
        //        return $this->isOwnedBy($user) || $user->is_admin;
        return $user->is_admin;
    }
}
