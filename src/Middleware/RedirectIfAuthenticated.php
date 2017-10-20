<?php

namespace ComposeDe\Blogify\Middleware;

use Closure;
use ComposeDe\Blogify\Facades\BlogifyAuth;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;

class RedirectIfAuthenticated
{
    protected $auth;

    /**
     * Create a new filter instance.
     *
     */
    public function __construct()
    {
        $this->auth = BlogifyAuth::getFacadeRoot();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->check()) {
            return new RedirectResponse(url('/home'));
        }

        return $next($request);
    }
}
