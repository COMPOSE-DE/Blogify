<?php

namespace ComposeDe\Blogify\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use BlogifyRoleModel;

class BlogifyAdminAuthenticate
{

    /**
     * The Guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * @var \ComposeDe\Blogify\Models\Role
     */
    private $adminRoles;

    /**
     * @var array
     */
    private $allowed_roles = [];

    /**
     * Create a new filter instance.
     *
     * @param \Illuminate\Contracts\Auth\Guard                 $auth
     * @param \BlogifyRoleModel|\ComposeDe\Blogify\Models\Role $roles
     */
    public function __construct(Guard $auth, BlogifyRoleModel $roles)
    {
        $this->auth = $auth;
        $this->adminRoles = $roles->byAdminRoles()->get();
        $this->fillAllowedRolesArray();
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
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            }

            return redirect()->route('admin.login');
        }

        // Check if the user has permission to visit the admin panel
        if (array_intersect($this->auth->user()->roles->pluck('id')->all(), $this->allowed_roles)) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }

    /**
     * @return void
     */
    private function fillAllowedRolesArray()
    {
        foreach ($this->adminRoles as $role) {
            array_push($this->allowed_roles, $role->id);
        }
    }

}
