<?php declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{id}', function (User $user, string $id): bool {
    return $user->id === (int) $id;
});

Broadcast::channel('news', function (): bool {
    return true;
});
