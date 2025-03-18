<?php

namespace App\Http\Middleware;

use Closure;

class Admin
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
        // Check if the user is logged in and is an admin
        if (auth()->check() && auth()->user()->role == 'admin') {
            return $next($request);
        }
    
        // Flash an error message and redirect to home if not admin
        request()->session()->flash('error', 'You do not have permission to access this page');
        return redirect()->route('home');
    }
}
