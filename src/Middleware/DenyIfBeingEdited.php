<?php

namespace ComposeDe\Blogify\Middleware;

use Closure;
use ComposeDe\Blogify\Facades\BlogifyAuth;
use BlogifyPostModel;

class DenyIfBeingEdited
{
    protected $auth;

    /**
     * @var \ComposeDe\Blogify\Models\Post
     */
    protected $posts;

    protected $users;

    /**
     * Create a new filter instance.
     *
     * @param \BlogifyPostModel $posts
     */
    public function __construct(BlogifyPostModel $posts)
    {
        $this->auth = BlogifyAuth::getFacadeRoot();
        $this->posts = $posts;
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
        $hash = $request->segment(3);
        $post = $this->posts->byHash($hash);

        if (
            $post->being_edited_by != null &&
            $post->being_edited_by != $this->auth->id()
        ) {
            $user = $this->users->find($post->being_edited_by)->fullName;

            session()->flash('notify', ['danger', trans('blogify::posts.notify.being_edited', ['name' => $user])]);
            return redirect()->route('posts.index');
        }

        return $next($request);
    }

}
