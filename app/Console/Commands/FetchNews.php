<?php declare(strict_types=1);

namespace App\Console\Commands;

use App\Events\NewsReceived;
use App\Models\Article;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FetchNews extends Command
{
    /**
     * @var string
     */
    protected $signature = 'news:fetch';

    /**
     * @var string
     */
    protected $description = 'Fetch latest news from https://newsapi.org';

    public function handle(): int
    {
        $apiKey = config('services.newsapi-org.key');
        throw_unless($apiKey, new \InvalidArgumentException('NEWSAPI_ORG_KEY is not set'));

        $articles = Http::get('https://newsapi.org/v2/top-headlines', [
            'apiKey' => $apiKey,
            'country' => 'us',
            'pageSize' => 100,
        ])->json('articles');

        if (app()->isLocal()) {
            Storage::put('articles.json', json_encode($articles, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }

        if (! is_array($articles)) {
            $this->error('Unrecognized api response');

            return self::FAILURE;
        }

        $urls = [];
        $articles = array_filter($articles, function (array $item) use (&$urls) {
            if ($item['title'] === '[Removed]') {
                return false;
            }

            $urls[] = $item['url'];

            return true;
        });

        $existingArticles = Article::query()
            ->whereIn('url', $urls)
            ->get()
            ->keyBy('url');

        $count = 0;
        foreach (Arr::sort($articles, 'publishedAt') as $item) {
            $article = $existingArticles[$item['url']] ?? new Article;

            $article->fill([
                'created_at' => CarbonImmutable::parse($item['publishedAt']),
                'title' => $item['title'],
                'content' => $item['content'],
                'source' => $item['source']['name'],
                'url' => $item['url'],
                'image_url' => $item['urlToImage'],
            ]);

            if ($article->isDirty()) {
                $article->save();
                $count++;
            }
        }

        if ($count) {
            event(new NewsReceived);
        }

        $this->line('News fetched successfully');

        return self::SUCCESS;
    }
}
