<?php

namespace ComposeDe\Blogify\Controllers\Admin;

use App\User;
use ComposeDe\Blogify\Facades\BlogifyAuth;
use ComposeDe\Blogify\Models\Comment;
use ComposeDe\Blogify\Models\Post;
use Illuminate\Contracts\Auth\Guard;

class DashboardController extends BaseController
{

    /**
     * @var \App\User
     */
    protected $user;

    /**
     * @var \ComposeDe\Blogify\Models\Post
     */
    protected $posts;

    /**
     * @var \ComposeDe\Blogify\Models\Comment
     */
    protected $comments;

    /**
     * Holds the data for the dashboard
     *
     * @var array
     */
    protected $data = [];

    /**
     * @param \App\User                         $user
     * @param \ComposeDe\Blogify\Models\Post    $posts
     * @param \ComposeDe\Blogify\Models\Comment $comments
     * @param \Illuminate\Contracts\Auth\Guard  $auth
     */
    public function __construct(Post $posts, Comment $comments) {
        parent::__construct();

        $this->posts = $posts;
        $this->comments = $comments;
        $this->user = BlogifyAuth::user();

        if ($this->user) {
            $this->{"buildDataArrayFor".$this->user->role->name}();
        }
    }

    ///////////////////////////////////////////////////////////////////////////
    // View methods
    ///////////////////////////////////////////////////////////////////////////

    /**
     * Show the dashboard view
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view("blogify::admin.home", $this->data);
    }

    ///////////////////////////////////////////////////////////////////////////
    // Helper methods
    ///////////////////////////////////////////////////////////////////////////

    /**
     * @return void
     */
    private function buildDataArrayForAdmin()
    {
        $users = app(config('blogify.models.auth'));

        $this->data['new_users_since_last_visit'] = $users->newUsersSince($this->user->updated_at)->count();

        $this->data['pending_comments'] = $this->comments->byRevised(1)->count();

        $this->data['published_posts'] = $this->posts->where('publish_date', '<=', date('Y-m-d H:i:s'))->count();

        $this->data['pending_review_posts'] = $this->posts->whereStatusId(2)->count();
    }

    /**
     * @return void
     */
    private function buildDataArrayForAuthor()
    {
        $this->data['published_posts'] = $this->posts->where('publish_date', '<=', date('Y-m-d H:i:s'))
                                            ->forAuthor()
                                            ->count();

        $this->data['pending_review_posts'] = $this->posts->whereStatusId(2)->forAuthor()->count();

        $post_ids = $this->posts->forAuthor()->lists('id');
        $this->data['pending_comments'] = $this->comments->byRevised(1)->whereIn('post_id', $post_ids)->count();
    }

    /**
     * @return void
     */
    private function buildDataArrayForReviewer()
    {
        $this->data['pending_review_posts'] = $this->posts->whereStatusId(2)->forReviewer()->count();
    }

}
