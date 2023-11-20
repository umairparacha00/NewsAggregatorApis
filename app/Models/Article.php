<?php

namespace App\Models;

use App\Models\Concerns\ArticleHasFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use ArticleHasFilters;

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function scopeSearch(Builder $query): Builder
    {
        $searchQuery = request('q');

        return $query->when(! is_null($searchQuery) && $searchQuery !== '',
            fn (Builder $query) => $query->where('title', 'like', '%'.$searchQuery.'%')
                ->orWhere('content', 'like', '%'.$searchQuery.'%'));
    }
}
