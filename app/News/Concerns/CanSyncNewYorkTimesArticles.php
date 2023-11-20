<?php

namespace App\News\Concerns;

use App\Models\Article;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait CanSyncNewYorkTimesArticles
{
    /**
     * Maps external API response to the format
     * suitable for the Article model
     */
    public function mapResultValuesToTableFields(array $articles): array
    {
        return collect($articles)->map(function ($article) {
            return [
                'title' => $article['title'],
                'external_id' => $article['slug_name'],
                'url' => $article['url'],
                'content' => $article['abstract'],
                'category' => $article['section'],
                'source' => $this->name(),
                'author' => $article['byline'],
                'published_at' => convertToLocalTimeStamp($article['published_date']),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->all();
    }

    /**
     * Stores the articles in the database
     */
    public function storeArticles(array $articles): void
    {
        Article::query()->insert($articles);
    }

    /**
     * Returns the url to get the articles from
     */
    public function url(): string
    {
        return config('news.sources.nyt.url').'/content/all/all.json';
    }

    /**
     * Returns the articles from the API
     */
    public function getArticles(): array
    {
        $request = Http::withQueryParameters(['api-key' => config('news.sources.nyt.key'), 'limit' => 500])
            ->get($this->url());

        if ($request->status() !== 200) {
            // log error
            Log::error($this->name().' source error. check the response below');
            Log::error($request->json());

            return ['results' => []];
        }

        return $request->json()['results'];
    }

    /**
     * Synchronizes articles by storing only those
     * that do not exist in the database
     */
    public function syncArticles(): void
    {
        $articles = $this->getOnlyTheArticlesThatDoNotExistInTheDatabase(
            $this->mapResultValuesToTableFields($this->getArticles())
        );

        $this->storeArticles($articles);
    }

    /**
     * Returns only the articles that do not exist
     * in the database
     */
    protected function getOnlyTheArticlesThatDoNotExistInTheDatabase(array $articles): array
    {
        // get only external_ids from the articles
        $externalArticleIds = collect($articles)->pluck('external_id')->toArray();

        // find the articles that already exist
        // in the database with the same external_id
        $articlesIds = Article::query()
            ->whereIn('external_id', $externalArticleIds)
            ->select('external_id')
            ->pluck('external_id');

        // remove the found articles from the list and return the rest
        return collect($articles)->whereNotIn('external_id', $articlesIds)->toArray();
    }
}
