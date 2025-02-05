<?php declare(strict_types=1);

namespace Tests\Unit;

use App\Events\NewsReceived;
use App\Models\Article;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command;
use Tests\TestCase;

class FetchNewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_that_new_articles_are_fetched(): void
    {
        $articles = [];

        for ($i = 0; $i < 5; $i++) {
            $articles[] = $this->articleResponse();
        }

        $articles[3]['title'] = '[Removed]';

        Article::factory()->create([
            'url' => $articles[1]['url'],
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'https://newsapi.org/v2/top-headlines*' => [
                'articles' => $articles,
            ],
        ]);

        Event::fake();

        $this->artisan('news:fetch')->assertExitCode(Command::SUCCESS);

        unset($articles[3]);  // It should not be in the database

        $articleModels = Article::query()
            ->orderBy('created_at')
            ->get();

        $this->assertCount(count($articles), $articleModels);

        foreach (array_values(Arr::sort($articles, 'publishedAt')) as $i => $article) {
            $this->assertEquals(CarbonImmutable::parse($article['publishedAt']), $articleModels[$i]->created_at);
            $this->assertEquals($article['title'], $articleModels[$i]->title);
            $this->assertEquals($article['content'], $articleModels[$i]->content);
            $this->assertEquals($article['source']['name'], $articleModels[$i]->source);
            $this->assertEquals($article['url'], $articleModels[$i]->url);
            $this->assertEquals($article['urlToImage'], $articleModels[$i]->image_url);
        }

        Event::assertDispatched(NewsReceived::class);
    }

    public function test_that_old_articles_are_not_saved_or_dispatched(): void
    {
        $articles = [];

        for ($i = 0; $i < 2; $i++) {
            $articles[] = $article = $this->articleResponse();
            Article::create([
                'created_at' => CarbonImmutable::parse($article['publishedAt']),
                'title' => $article['title'],
                'content' => $article['content'],
                'source' => $article['source']['name'],
                'url' => $article['url'],
                'image_url' => $article['urlToImage'],
            ]);
        }

        Http::preventStrayRequests();
        Http::fake([
            'https://newsapi.org/v2/top-headlines*' => [
                'articles' => $articles,
            ],
        ]);

        Event::fake();

        $this->artisan('news:fetch')->assertExitCode(Command::SUCCESS);

        $articleModels = Article::query()
            ->orderBy('created_at')
            ->get();

        $this->assertCount(count($articles), $articleModels);

        Event::assertNotDispatched(NewsReceived::class);
    }

    public function test_invalid_api_response(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://newsapi.org/v2/top-headlines*' => [
                'error' => 'Some error',
            ],
        ]);

        Event::fake();

        $this->artisan('news:fetch')->assertExitCode(Command::FAILURE);

        Event::assertNotDispatched(NewsReceived::class);
    }

    private function articleResponse(): array
    {
        return [
            'source' => [
                'id' => Str::random(4),
                'name' => fake()->company,
            ],
            'author' => fake()->name,
            'title' => fake()->sentence,
            'description' => fake()->paragraph(2),
            'url' => fake()->url,
            'urlToImage' => fake()->url,
            'publishedAt' => fake()->dateTimeBetween('- 1 week', 'now')->format('Y-m-d\TH:i:sp'),
            'content' => fake()->paragraph(5),
        ];
    }
}
