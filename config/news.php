<?php

return [
    'sources' => [
        'nyt' => [
            'url' => env('NYT_API_URL'),
            'key' => env('NYT_API_KEY'),
            'class' => \App\News\NewYorkTimes::class,
        ],
    ],
];
