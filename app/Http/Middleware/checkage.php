<?php

namespace App\Http\Middleware;

use Closure;

class checkage
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
        if($request->path()!='unauthorized'){
            if($request->age && $request->age<20){
                return redirect("unauthorized");
            }
        }
        return $next($request);
    }
}
