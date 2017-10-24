<?php

namespace ComposeDe\Blogify\Middleware;

use Closure;
use ComposeDe\Blogify\Facades\BlogifyAuth;

class IsOwner
{
    protected $auth;

    protected $users;

    /**
     * Create a new filter instance
     */
    public function __construct()
    {
        $this->auth = BlogifyAuth::getFacadeRoot();
        $this->users = app(config('blogify.models.user'));
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $this->users->findOrFail($request->segment(3));

        if ($this->auth->id() != $user->id) {
            abort(404);
        }

        return $next($request);
    }
}
