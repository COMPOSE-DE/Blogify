<?php namespace ComposeDe\Blogify\Middleware;

use Closure;
use ComposeDe\Blogify\Facades\BlogifyAuth;
use Illuminate\Contracts\Auth\Guard;
use ComposeDe\Blogify\Models\Role;

class HasAdminOrAuthorRole
{
    protected $auth;

    /**
     * @var \ComposeDe\Blogify\Models\Role
     */
    private $roles;

    /**
     * @var array
     */
    private $allowed_roles = [];

    /**
     * Create a new filter instance.
     *
     * @param \ComposeDe\Blogify\Models\Role   $roles
     */
    public function __construct(Role $roles)
    {
        $this->auth = BlogifyAuth::getFacadeRoot();
        $this->roles = $roles;

        $this->fillAlowedRolesArray();
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
        if (! in_array($this->auth->user()->role->id, $this->allowed_roles)) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }

    /**
     * @return void
     */
    private function fillAlowedRolesArray()
    {
        $roles = $this->roles
                    ->where('name', '<>', 'reviewer')
                    ->where('name', '<>', 'member')
                    ->get();

        foreach ($roles as $role) {
            array_push($this->allowed_roles, $role->id);
        }
    }
}
