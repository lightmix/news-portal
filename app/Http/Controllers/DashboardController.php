<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Dashboard;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Dashboard $dashboard): Response
    {
        $data = $dashboard->handle();

        return Inertia::render('Dashboard', $data);
    }
}
