<?php

namespace App\Http\Middleware\API;

use Closure;
use Illuminate\Http\Request;

class SwaggerFixHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        /* if ($request->hasHeader("Authorization") && strpos($request->headers->get("Authorization"),"Bearer ") === false) {
            $request->headers->set("Authorization","Bearer ".$request->headers->get("Authorization"));
        } */

        if(!$request->hasHeader("content-type") /* || $request->headers->get("content-type") != "application/json" */)
        {
            $request->headers->set("content-type","application/json");
        }

        if(!$request->hasHeader("x-requested-with") /* || $request->headers->get("x-requested-with") != "XMLHttpRequest" */)
        {
            $request->headers->set("x-requested-with","XMLHttpRequest");
        }

        return $next($request);
    }
}
