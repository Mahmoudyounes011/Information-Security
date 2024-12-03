<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class CheckLangHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {   
        $locale = $request->header('Accept-Language', 
        Config::get('app.locale')); 

        if (in_array($locale, 
        Config::get('app.supported_locales', 
        ['en', 'ar']))) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
