<?php declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * @mixin Article
 */
class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (is_null($this->resource) || $this->resource instanceof MissingValue) {
            return [];
        }

        return [
            'id' => $this->id,
            'createdAt' => $this->created_at->format('c'),
            'title' => $this->title,
            'content' => $this->content ?? '',
            'url' => $this->url ?? '',
            'imageUrl' => $this->image_url ?? '',
            'source' => $this->source,
        ];
    }
}
