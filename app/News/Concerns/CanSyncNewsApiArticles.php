<?php

namespace App\News\Concerns;

use App\Models\Article;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait CanSyncNewsApiArticles
{
    /**
     * Maps external API response to the format
     * suitable for the Article model
     */
    public function mapResultValuesToTableFields(array $articles): array
    {
        return collect($articles)
            ->reject(fn ($article) => $article['title'] === '[Removed]')
            ->map(function ($article) {
                return [
                    'title' => $article['title'],
                    'url' => $article['url'],
                    'content' => $article['content'],
                    'source' => $article['source']['name'],
                    'author' => $article['author'],
                    'published_at' => convertToLocalTimeStamp($article['publishedAt']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->all();
    }

    /**
     * Synchronizes articles by storing only those
     * that do not exist in the database
     */
    public function syncArticles(): void
    {
        $articles = $this->getTheArticlesThatDoNotExistInTheDatabase(
            $this->mapResultValuesToTableFields($this->getArticles())
        );

        $this->storeArticles($articles);
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
        return config('news.sources.news_api.url').'/top-headlines';
    }

    /**
     * Returns the articles from the API
     */
    public function getArticles(): array
    {
        $request = Http::withToken(config('news.sources.news_api.key'))
            ->withQueryParameters(['language' => 'en', 'pageSize' => 100])
            ->get($this->url());

        if ($request->status() !== 200) {
            // log error
            Log::error($this->name().' source error. check the response below');
            Log::error($request->json());

            return ['articles' => []];
        }

        return $request->json()['articles'];
    }

    /**
     * Returns the articles that do not exist in the database
     */
    protected function getTheArticlesThatDoNotExistInTheDatabase(array $articles): array
    {
        // get only title from the articles
        $externalArticleTitles = collect($articles)->pluck('title')->toArray();

        // find the articles that already exist
        // in the database with the same title
        $articlesTitles = Article::query()
            ->whereIn('title', $externalArticleTitles)
            ->select('title')
            ->pluck('title');

        // remove the found articles from the list and return the rest
        return collect($articles)->whereNotIn('title', $articlesTitles)->toArray();

    }
}
