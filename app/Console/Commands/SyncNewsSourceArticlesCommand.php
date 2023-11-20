<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncNewsSourceArticlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync articles from all news sources';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        foreach (app('newsSources') as $source) {
            $this->info("Syncing {$source->name()} articles");

            $source->syncArticles();

            $this->info("{$source->name()} articles synced");
        }

        return 0;
    }
}
