<?php

return [
    'sources' => [
        'nyt' => [
            'url' => env('NYT_API_URL'),
            'key' => env('NYT_API_KEY'),
            'class' => \App\News\NewYorkTimes::class,
        ],
        'news_api' => [
            'url' => env('NEWSAPI_API_URL'),
            'key' => env('NEWSAPI_API_KEY'),
            'class' => \App\News\NewsApi::class,
        ],
        'guardian' => [
            'url' => env('GUARDIAN_API_URL'),
            'key' => env('GUARDIAN_API_KEY'),
            'class' => \App\News\TheGuardian::class,
        ],

    ],
];
