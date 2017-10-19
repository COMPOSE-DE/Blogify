<?php

namespace ComposeDe\Blogify\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use ComposeDe\Blogify\Models\Post;

class CanViewPost
{

    /**
     * The Guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * @var \ComposeDe\Blogify\Models\Post
     */
    protected $posts;

    /**
     * Create a new filter instance.
     *
     * @param \ComposeDe\Blogify\Models\Post   $posts
     * @param \Illuminate\Contracts\Auth\Guard $auth
     */
    public function __construct(Guard $auth, Post $posts)
    {
        $this->auth = $auth;
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
        $user_id = $this->auth->user()->getAuthIdentifier();

        if ($post->visibility_id == 'Private') {
            if (! $post->user_id == $user_id) {
                return false;
            }
        }

        return true;
    }
}
