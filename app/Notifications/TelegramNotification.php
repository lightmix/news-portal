<?php declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

final class TelegramNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $message,
    ) {}

    public function via(User|AnonymousNotifiable $notifiable): array
    {
        return [TelegramChannel::class];
    }

    public function toTelegram(User|AnonymousNotifiable $notifiable): TelegramMessage
    {
        return TelegramMessage::create($this->message)
            ->token(config('services.telegram-bot.token'));
    }
}
