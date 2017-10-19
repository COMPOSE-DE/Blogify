<?php

namespace ComposeDe\Blogify\Controllers\Admin;

use App\User;
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
    protected $post;

    /**
     * @var \ComposeDe\Blogify\Models\Comment
     */
    protected $comment;

    /**
     * Holds the data for the dashboard
     *
     * @var array
     */
    protected $data = [];

    /**
     * @param \App\User $user
     * @param \ComposeDe\Blogify\Models\Post $post
     * @param \ComposeDe\Blogify\Models\Comment $comment
     * @param \Illuminate\Contracts\Auth\Guard $auth
     */
    public function __construct(Post $post, Comment $comment) {
        parent::__construct();

        $this->post = $post;
        $this->comment = $comment;

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
        $this->data['new_users_since_last_visit'] = User::newUsersSince($this->user->updated_at)->count();

        $this->data['pending_comments'] = $this->comment->byRevised(1)->count();

        $this->data['published_posts'] = $this->post->where('publish_date', '<=', date('Y-m-d H:i:s'))->count();

        $this->data['pending_review_posts'] = $this->post->whereStatusId(2)->count();
    }

    /**
     * @return void
     */
    private function buildDataArrayForAuthor()
    {
        $this->data['published_posts'] = $this->post->where('publish_date', '<=', date('Y-m-d H:i:s'))
                                            ->forAuthor()
                                            ->count();

        $this->data['pending_review_posts'] = $this->post->whereStatusId(2)->forAuthor()->count();

        $post_ids = $this->post->forAuthor()->lists('id');
        $this->data['pending_comments'] = $this->comment->byRevised(1)->whereIn('post_id', $post_ids)->count();
    }

    /**
     * @return void
     */
    private function buildDataArrayForReviewer()
    {
        $this->data['pending_review_posts'] = $this->post->whereStatusId(2)->forReviewer()->count();
    }

}
