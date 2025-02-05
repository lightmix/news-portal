<?php declare(strict_types=1);

namespace Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_at' => CarbonImmutable::createFromInterface(fake()->dateTimeBetween('- 7 days', 'now')),
            'title' => fake()->sentence,
            'content' => fake()->paragraph,
            'source' => fake()->company,
            'url' => fake()->url,
            'image_url' => fake()->url,
        ];
    }
}
