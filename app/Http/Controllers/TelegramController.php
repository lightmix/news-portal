<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\ConfirmRegistration;
use App\Encryption\TokenManager;
use App\Events\RegistrationCompleted;
use App\Models\User;
use App\Notifications\TelegramNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TelegramController extends Controller
{
    public function update(Request $request): void
    {
        $data = $request->toArray();
        if (Arr::get($data, 'my_chat_member.new_chat_member.status') === 'kicked') {
            $telegramId = Arr::get($data, 'my_chat_member.chat.id');

            User::query()
                ->where('telegram_id', $telegramId)
                ->update(['telegram_id' => null]);

            return;
        }

        $message = $data['message'] ?? [];

        if (! $this->parseEntities($message)) {
            $this->parseText($message);
        }
    }

    private function parseEntities(array $message): bool
    {
        if (empty($message['entities'])) {
            return false;
        }

        $text = $message['text'] ?? '';
        foreach ($message['entities'] as $entity) {
            if ($entity['type'] !== 'bot_command') {
                continue;
            }

            $command = mb_substr($text, $entity['offset'] + 1, $entity['length'] - 1);
            $params = mb_substr($text, $entity['offset'] + $entity['length'] + 1);
            switch ($command) {
                case 'start':
                    $this->start($message['chat']['id'], $params);

                    return true;

                    // More commands here
            }

            return false;
        }

        return false;
    }

    private function parseText(array $message): void
    {
        $this->start($message['chat']['id'], $message['text']);
    }

    private function start(int $chatId, string $token): void
    {
        /** @var TokenManager $tokenManager */
        $tokenManager = app(TokenManager::class);
        $accessToken = $tokenManager->findToken(trim($token));
        if (! $accessToken?->can(ConfirmRegistration::ABILITY)) {
            return;
        }

        /** @var ?User $user */
        if (! $user = $accessToken->tokenable) {
            $accessToken->delete();

            return;
        }

        $userJustConfirmed = false;
        $message = '';
        if ($user->confirmed_at && $user->telegram_id === $chatId) {
            $message = 'Welcome back.';
        } else {
            if (! $user->confirmed_at) {
                $userJustConfirmed = true;
                $message = "Registration completed successfully!\n";
            }
            if ($user->telegram_id && $user->telegram_id !== $chatId) {
                $message .= "Your Telegram account has been changed.\n";
            }
        }

        DB::transaction(static function () use ($user, $chatId, $accessToken) {
            $user->telegram_id = $chatId;
            $user->confirmed_at ??= now();
            $user->save();
            $accessToken->delete();
        }, 5);

        if ($message) {
            $user->notify(new TelegramNotification($message));
        }

        if ($userJustConfirmed) {
            event(new RegistrationCompleted($user));
        }
    }
}
