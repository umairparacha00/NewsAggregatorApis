<?php

namespace App\News\Contracts;

interface NewsSource
{
    public function name(): string;

    public function syncArticles(): void;
}
