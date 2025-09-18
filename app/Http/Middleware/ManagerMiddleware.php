<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class ManagerMiddleware
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
        if (Auth::check() && Auth::user()->is_manager) {
            return $next($request);
        }
        return redirect('/')->with('status',' Увас нет прав просмотра этой страницы');
    }
}
