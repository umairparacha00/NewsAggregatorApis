<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait ArticleHasFilters
{
    public function scopeSourceFilter(Builder $query): Builder
    {
        $sourceParameter = request('source');

        return $query->when(is_string($sourceParameter),
            fn (Builder $query) => $query->whereIn('source', explode(',', $sourceParameter)));
    }

    public function scopeCategoryFilter(Builder $query): Builder
    {
        $categoryParameter = request('category');

        return $query->when(is_string($categoryParameter),
            fn (Builder $query) => $query->whereIn('category', explode(',', $categoryParameter)));
    }

    public function scopeAuthorFilter(Builder $query): Builder
    {
        $authorParameter = request('author');

        return $query->when(is_string($authorParameter),
            fn (Builder $query) => $query->whereIn('author', explode(',', $authorParameter)));
    }

    public function scopeFilters(Builder $query): Builder
    {
        // add filter with date, category, source, author
        return $query->categoryFilter()
            ->sourceFilter()
            ->authorFilter()
            ->publishedDateFilter();
    }

    public function scopePublishedDateFilter(Builder $query): Builder
    {
        $publishedDateParameter = request('publishedDate');

        return $query->when(is_string($publishedDateParameter),
            fn (Builder $query) => $query->whereDate('published_at', $publishedDateParameter));
    }
}
