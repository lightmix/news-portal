<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Laravel\Telescope\Telescope;

class AuthorizeTelescope
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        return Telescope::check($request) ? $next($request) : abort(404);
    }
}
