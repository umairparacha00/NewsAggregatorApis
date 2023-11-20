<?php

namespace App\Http\Controllers;

use App\Models\Article;

class AuthorController extends Controller
{
    public function __invoke()
    {
        // get all the unique authors from the articles table on author column
        $authors = Article::select('author')
            ->whereNotNull('author')
            ->where('author', '!=', '')
            ->distinct()->pluck('author');

        return apiResponse($authors);
    }
}
