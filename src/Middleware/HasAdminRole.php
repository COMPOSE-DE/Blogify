<?php

namespace ComposeDe\Blogify\Middleware;

use Closure;
use ComposeDe\Blogify\Facades\BlogifyAuth;
use BlogifyRoleModel;

class HasAdminRole
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->user()->getHighestRole()->name != app(BlogifyRoleModel::class)->getAdminRoleName()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
