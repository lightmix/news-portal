<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;

class DeleteExpiredTokens extends Command
{
    /**
     * @var string
     */
    protected $signature = 'tokens:delete:expired';

    protected $description = 'Delete expired tokens';

    public function handle(): int
    {
        $deleted = PersonalAccessToken::query()
            ->where('expires_at', '<=', Carbon::now())->delete();

        $this->line("$deleted token(s) deleted.");

        return self::SUCCESS;
    }
}
