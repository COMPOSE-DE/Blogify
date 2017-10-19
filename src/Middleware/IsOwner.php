<?php

namespace ComposeDe\Blogify\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use App\User;

class IsOwner
{

    /**
     * The Guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * @var \App\User
     */
    protected $users;

    /**
     * Create a new filter instance.
     *
     * @param \Illuminate\Contracts\Auth\Guard $auth
     * @param \App\User                        $users
     */
    public function __construct(Guard $auth, User $users)
    {
        $this->auth = $auth;
        $this->users = $users;
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

        if ($this->auth->user()->getAuthIdentifier() != $user->id) {
            abort(404);
        }

        return $next($request);
    }
}
