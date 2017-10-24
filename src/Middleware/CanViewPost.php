<?php

namespace ComposeDe\Blogify\Middleware;

use Closure;
use ComposeDe\Blogify\Facades\BlogifyAuth;
use BlogifyPostModel;

class CanViewPost
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
        if (! $this->checkIfUserCanViewPost($request)) {
            return redirect()->route('/');
        }

        return $next($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    private function checkIfUserCanViewPost($request)
    {
        $post = $this->posts->byHash($request->segment(3));
        $user_id = $this->auth->id();

        if ($post->visibility_id == 'Private') {
            if (! $post->user_id == $user_id) {
                return false;
            }
        }

        return true;
    }
}
