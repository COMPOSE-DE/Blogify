<?php namespace ComposeDe\Blogify\Middleware;

use Closure;
use ComposeDe\Blogify\Facades\BlogifyAuth;
use BlogifyRoleModel;

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
     * @param \BlogifyRoleModel $roles
     */
    public function __construct(BlogifyRoleModel $roles)
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
        if (! in_array($this->auth->user()->getHighestRole()->id, $this->allowed_roles)) {
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
