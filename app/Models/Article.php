<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property string $title
 * @property null|string $content
 * @property string $source
 * @property string $url
 * @property null|string $image_url
 */
class Article extends Model
{
    use HasFactory;

    protected $guarded = [];
}
