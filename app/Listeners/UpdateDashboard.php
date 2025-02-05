<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\DashboardUpdated;
use App\Events\NewsReceived;
use Illuminate\Auth\Events\Registered;

readonly class UpdateDashboard
{
    /**
     * Handle the event.
     */
    public function handle(Registered|NewsReceived $event): void
    {
        event(new DashboardUpdated);
    }
}
