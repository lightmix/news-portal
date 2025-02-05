<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class ProtectGuestUser
{
    public const string GUEST_USER_EMAIL = 'guest@example.com';

    public const string GUEST_USER_MSG = 'Sorry, Guest user is not allowed to change profile. But you can register your own account.';

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()?->email === self::GUEST_USER_EMAIL) {
            return back()->withErrors(['general' => __(self::GUEST_USER_MSG)]);
        }

        return $next($request);
    }
}
