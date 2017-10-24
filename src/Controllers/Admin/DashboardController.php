<?php

namespace ComposeDe\Blogify\Controllers\Admin;

use ComposeDe\Blogify\Facades\BlogifyAuth;
use BlogifyPostModel;
use BlogifyCommentModel;
use BlogifyRoleModel;


class DashboardController extends BaseController
{
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
     * @var \ComposeDe\Blogify\Models\Role
     */
    protected $roles;

    /**
     * Holds the data for the dashboard
     *
     * @var array
     */
    protected $data = [];

    /**
     * @param \BlogifyPostModel    $posts
     * @param \BlogifyCommentModel $comments
     * @param \BlogifyRoleModel    $roles
     */
    public function __construct(BlogifyPostModel $posts, BlogifyCommentModel $comments, BlogifyRoleModel $roles) {
        parent::__construct();

        $this->posts = $posts;
        $this->comments = $comments;
        $this->user = BlogifyAuth::user();
        $this->roles = $roles;

        if ($this->user) {
            $highestRoleName = $this->user->getHighestRole()->name;

            switch($highestRoleName)
            {
                case $this->roles->getAdminRoleName():
                    $this->buildDataArrayForAdmin();
                    break;
                case $this->roles->getAuthorRoleName():
                    $this->buildDataArrayForAuthor();
                    break;
                case $this->roles->getReviewerRoleName():
                    $this->buildDataArrayForReviewer();
                    break;
            }
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
        $users = app(config('blogify.models.user'));

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
