<?php
/**
 * Created by PhpStorm.
 * User: alexs
 * Date: 25.01.2018
 * Time: 22:50
 */

namespace App\Http\Middleware;
use App\Support\Google2FAAuthenticator;

class Google2FAMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $authenticator = app(Google2FAAuthenticator::class)->boot($request);

        if ($authenticator->isAuthenticated()) {
            return $next($request);
        }

        return $authenticator->makeRequestOneTimePasswordResponse();
    }
}