<?php

namespace App\News;

use App\News\Concerns\CanSyncNewsApiArticles;
use App\News\Contracts\NewsSource;

class NewsApi implements NewsSource
{
    use CanSyncNewsApiArticles;

    public function name(): string
    {
        return 'News Api';
    }
}
