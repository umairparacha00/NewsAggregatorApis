<?php

namespace App\Http\Controllers;

class SourceController extends Controller
{
    public function __invoke()
    {
        // get all the unique sources from the articles table on source column
        $sources = app('newsSources')->map(function ($source) {
            return ['name' => $source->name()];
        })->pluck('name');

        return apiResponse($sources);
    }
}
