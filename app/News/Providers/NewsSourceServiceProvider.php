<?php

namespace App\News\Providers;

use Illuminate\Support\ServiceProvider;

class NewsSourceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('newsSources', function () {
            return collect(config('news.sources'))->map(function ($source) {
                return new $source['class']();
            });
        });
    }
}
