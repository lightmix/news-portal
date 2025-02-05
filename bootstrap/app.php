<?php declare(strict_types=1);

use App\Http\Middleware\EnsureRegistrationConfirmed;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [__DIR__.'/../routes/web.php', __DIR__.'/../routes/auth.php'],
        api: __DIR__.'/../routes/api-v1.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        apiPrefix: 'api/v1',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            EnsureRegistrationConfirmed::class,
        ]);

        $middleware->api(append: [
            EnsureRegistrationConfirmed::class,
        ]);

        $middleware->trustProxies(at: [
            '10.0.0.0/16',
            '172.16.0.0/12',
        ], headers: Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_PROTO
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('news:fetch')
            ->environments(['production'])
            ->everyThirtyMinutes();

        $schedule->command('tokens:delete:expired')
            ->daily();

        $schedule->command('telescope:prune --hours='.config('telescope.prune_after_hours'))
            ->daily();
    })
    ->create();
