<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Models\Article;

class ArticleController extends Controller
{
    public function __invoke()
    {
        $articles = Article::query()
            ->select(['id', 'title', 'url', 'content', 'category', 'author', 'source', 'published_at'])
            ->search()->filters()
            ->paginate(getPerPageQueryParameter(request('perPage')));

        return ArticleResource::collection($articles);
    }
}
