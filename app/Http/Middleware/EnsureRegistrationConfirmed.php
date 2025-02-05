<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegistrationConfirmed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (
            ! $user
            || $request->route()->getName() === 'logout'
            || in_array($request->route()->uri(), ['broadcasting/auth', 'broadcasting/user-auth'], true)
            || ($user->confirmed_at xor $request->route()->getName() === 'registration.confirm')
        ) {
            return $next($request);
        }

        if ($user->confirmed_at) {
            return $request->expectsJson()
                ? abort(403, 'Your registration is already confirmed.')
                : Redirect::route('dashboard');
        }

        return $request->expectsJson()
            ? abort(403, 'Your registration is not confirmed.')
            : Redirect::route('registration.confirm');
    }
}
