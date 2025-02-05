<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\ConfirmRegistration;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ConfirmRegistrationController extends Controller
{
    public function show(ConfirmRegistration $confirmRegistration): Response
    {
        $data = $confirmRegistration->handle(auth()->user());

        return Inertia::render('Auth/ConfirmRegistration', $data);
    }
}
