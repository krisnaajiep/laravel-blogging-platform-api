<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'category',
        'tags',
    ];

    /**
     * Scope a query to search posts.
     */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        $query->when($term ?? false, function () use ($query, $term) {
            $query->where('title', 'like', "%$term%")
                ->orWhere('content', 'like', "%$term%")
                ->orWhere('category', 'like', "%$term%");
        });
    }
}
