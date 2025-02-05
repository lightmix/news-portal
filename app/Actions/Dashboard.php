<?php declare(strict_types=1);

namespace App\Actions;

use App\Models\Article;
use App\Models\User;

final class Dashboard
{
    public function handle(): array
    {
        return [
            'p' => [
                'totalUsers' => User::withTrashed()->count(),
                'lastRegistrationAt' => User::withTrashed()->latest()->value('created_at')->format('c'),
                'totalNews' => Article::query()->count(),
            ],
        ];
    }
}
