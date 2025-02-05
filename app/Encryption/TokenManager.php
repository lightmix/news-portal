<?php declare(strict_types=1);

namespace App\Encryption;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

final class TokenManager
{
    public function createToken(Model $tokenable, string $name, array $abilities = ['*'], ?\DateTimeInterface $expiresAt = null): NewAccessToken
    {
        $plainTextToken = $tokenable->generateTokenString();

        $token = $tokenable->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
        ]);

        return new NewAccessToken($token, $plainTextToken);
    }

    public function findToken(?string $token): ?PersonalAccessToken
    {
        if (empty($token)) {
            return null;
        }

        $accessToken = Sanctum::$personalAccessTokenModel::findToken($token);

        return ! $accessToken?->expires_at?->isPast() ? $accessToken : null;
    }
}
