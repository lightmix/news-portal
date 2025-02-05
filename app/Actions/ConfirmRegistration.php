<?php declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Encryption\TokenManager;

final class ConfirmRegistration
{
    public const string ABILITY = 'confirm-registration';

    public function __construct(
        private readonly TokenManager $tokenManager,
    ) {}

    public function handle(User $user): array
    {
        $token = $this->tokenManager->createToken($user, 'confirm-registration', [self::ABILITY], now()->addDays(3))->plainTextToken;

        return [
            'tgBotLink' => 'https://t.me/'.config('services.telegram-bot.username'),
            'token' => $token,
        ];
    }
}
