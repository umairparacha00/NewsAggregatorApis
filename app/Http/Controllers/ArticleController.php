<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Models\Article;

class ArticleController extends Controller
{
    public function __invoke()
    {
        // check if the request has a perPage query parameter
        // if it does, use that value, otherwise use the default value
        $perPage = is_numeric(request('perPage')) && request('perPage') <= 50 ? request('perPage') : 10;

        $articles = Article::query()
            ->select(['id', 'title', 'url', 'content', 'category', 'author', 'source', 'published_at'])
            ->search()->filters()
            ->paginate($perPage);

        return ArticleResource::collection($articles);
    }
}
