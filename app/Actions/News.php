<?php declare(strict_types=1);

namespace App\Actions;

use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;

final class News
{
    public function handle(?string $search, ?int $page): array
    {
        $news = Article::query()
            ->when(filled($search), fn (Builder $query) => $query->whereFullText(['title', 'content'], $search))
            ->latest()
            ->paginate(10, page: $page);

        return [
            'news' => ArticleResource::collection($news),
        ];
    }
}
