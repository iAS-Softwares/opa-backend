<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SameSiteNoneMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);


        config([
            'session.same_site' => null, // Set the desired same_site value
        ]);

        return $response;
    }
}
