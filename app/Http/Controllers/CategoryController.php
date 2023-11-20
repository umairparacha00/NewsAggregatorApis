<?php

namespace App\Http\Controllers;

use App\Models\Article;

class CategoryController extends Controller
{
    public function __invoke()
    {
        // get all the unique categories from the articles table on category column
        $categories = Article::select('category')
            ->whereNotNull('category')
            ->distinct()->pluck('category');

        return apiResponse($categories);
    }
}
