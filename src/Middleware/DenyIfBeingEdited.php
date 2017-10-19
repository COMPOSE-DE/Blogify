<?php

namespace ComposeDe\Blogify\Middleware;

use App\User;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use ComposeDe\Blogify\Models\Post;

class DenyIfBeingEdited
{

    /**
     * Holds the Guard Contract
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * @var \ComposeDe\Blogify\Models\Post
     */
    protected $posts;

    /**
     * @var \App\User
     */
    protected $users;

    /**
     * Create a new filter instance.
     *
     * @param \Illuminate\Contracts\Auth\Guard $auth
     * @param \ComposeDe\Blogify\Models\Post   $posts
     * @param \App\User                        $users
     */
    public function __construct(Guard $auth, Post $posts, User $users)
    {
        $this->auth = $auth;
        $this->posts = $posts;
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
        $hash = $request->segment(3);
        $post = $this->posts->byHash($hash);

        if (
            $post->being_edited_by != null &&
            $post->being_edited_by != $this->auth->user()->getAuthIdentifier()
        ) {
            $user = $this->users->find($post->being_edited_by)->fullName;

            session()->flash('notify', ['danger', trans('blogify::posts.notify.being_edited', ['name' => $user])]);
            return redirect()->route('posts.index');
        }

        return $next($request);
    }

}
