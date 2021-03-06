<?php

namespace ComposeDe\Blogify\Middleware;

use Closure;
use ComposeDe\Blogify\Facades\BlogifyAuth;
use ComposeDe\Blogify\Facades\BlogifyRole;
use BlogifyPostModel;

class CanEditPost
{
    protected $auth;

    /**
     * @var \ComposeDe\Blogify\Models\Post
     */
    protected $posts;

    /**
     * Create a new filter instance.
     *
     * @param \BlogifyPostModel $posts
     */
    public function __construct(BlogifyPostModel $posts)
    {
        $this->auth = BlogifyAuth::getFacadeRoot();
        $this->posts = $posts;
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
        if (! $this->checkIfUserCanEditPost($request)) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    private function checkIfUserCanEditPost($request)
    {
        $post = $this->posts->byHash($request->segment(3));
        $user_id = $this->auth->id();

        if (
            $user_id != $post->user_id &&
            $user_id != $post->reviewer_id &&
            $this->auth->user()->getHighestRole()->name != BlogifyRole::getAdminRoleName()
        ) {
            return false;
        }

        return true;
    }

}
