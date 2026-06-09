<?php

namespace App\Http\Middleware;

use Closure;
use Cookie;

class LanguageSelectionCookies
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $cookie = Cookie::queue(Cookie::forget('language_selected'));

        $value = Cookie::get('language_selected');
        
        if($value!='')
        {
            Cookie::queue('language_selected', $value, 60*60*24*3);
            app()->setLocale($value);
        
        }else{
            app()->setLocale('en');
        }
        return $next($request);
    }
}
