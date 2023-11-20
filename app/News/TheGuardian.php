<?php

namespace App\News;

use App\News\Concerns\CanSyncTheGuardianArticles;
use App\News\Contracts\NewsSource;

class TheGuardian implements NewsSource
{
    use canSyncTheGuardianArticles;

    public function name(): string
    {
        return 'The Guardian';
    }
}
