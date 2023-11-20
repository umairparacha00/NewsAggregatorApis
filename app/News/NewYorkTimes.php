<?php

namespace App\News;

use App\News\Concerns\CanSyncNewYorkTimesArticles;
use App\News\Contracts\NewsSource;

class NewYorkTimes implements NewsSource
{
    use CanSyncNewYorkTimesArticles;

    public function name(): string
    {
        return 'New York Times';
    }
}
