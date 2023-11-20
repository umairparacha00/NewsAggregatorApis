<?php

namespace App\News\Concerns;

use App\Models\Article;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait CanSyncTheGuardianArticles
{
    /**
     * Maps external API response to the format
     * suitable for the Article model
     */
    public function mapResultValuesToTableFields(array $articles): array
    {
        return collect($articles)
            ->reject(fn ($article) => $article['type'] !== 'article')
            ->map(function ($article) {
                return [
                    'external_id' => $article['id'],
                    'title' => $article['webTitle'],
                    'url' => $article['webUrl'],
                    'content' => $article['fields']['bodyText'],
                    'category' => $article['pillarName'] ?? null,
                    'source' => $this->name(),
                    'author' => $article['fields']['byline'] ?? null,
                    'published_at' => convertToLocalTimeStamp($article['webPublicationDate']),
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
     * Synchronizes articles by storing only those
     * that do not exist in the database
     */
    public function syncArticles(): void
    {
        $articles = $this->getTheArticlesThatDoNotExistInTheDatabase(
            $this->mapResultValuesToTableFields($this->getArticles()['results']), 2
        );

        // chunk the articles and store them
        collect($articles)->chunk(100)
            ->each(function ($articles) {
                foreach ($articles as $article) {
                    $this->storeArticles($article);
                }
            });
    }

    /**
     * Returns the articles from the API
     */
    public function getArticles(int $pageSize = 50, int $page = 1): mixed
    {
        $request = Http::withQueryParameters(['api-key' => config('news.sources.guardian.key'), 'show-fields' => 'byline,bodyText', 'page-size' => $pageSize, 'page' => $page])
            ->get($this->url());

        if ($request->status() !== 200) {
            // log error
            Log::error($this->name().' source error. check the response below');
            Log::error($request->json());

            return ['results' => []];
        }

        return $request->json()['response'];
    }

    /**
     * Returns the url to get the articles from
     */
    public function url(): string
    {
        return config('news.sources.guardian.url').'/search';
    }

    /**
     * Returns only the articles that do not exist
     * in the database
     */
    protected function getTheArticlesThatDoNotExistInTheDatabase(array $articles, int $page): array
    {
        // get only external_ids from the articles
        $externalArticleIds = collect($articles)->pluck('external_id')->toArray();

        // find the articles that already exist in the database
        $articlesIds = Article::query()
            ->whereIn('external_id', $externalArticleIds)
            ->select('external_id')
            ->pluck('external_id');

        /* get the next page of articles and check if any of them exist in the database
         then stop the recursion and return the articles
         else continue the recursion
        */
        if ($articlesIds->count() === 0 && $page <= 10) {
            $recursionArticles = $this->getTheArticlesThatDoNotExistInTheDatabase(
                $this->mapResultValuesToTableFields($this->getArticles(page: $page)['results']), $page + 1
            );

            // merge the results and return them
            return collect($articles)->merge($recursionArticles)->values()->all();
        }

        // remove the found articles from the list and return the rest
        return collect($articles)->whereNotIn('external_id', $articlesIds)->toArray();
    }
}
