<?php

namespace ComposeDe\Blogify\Controllers\Admin;

use Carbon\Carbon;
use ComposeDe\Blogify\Blogify;
use BlogifyTagModel;
use BlogifyRoleModel;
use BlogifyPostModel;
use BlogifyStatusModel;
use BlogifyCategoryModel;
use BlogifyVisibilityModel;
use ComposeDe\Blogify\Models\Status;
use ComposeDe\Blogify\Models\Tag;
use ComposeDe\Blogify\Requests\ImageUploadRequest;
use Intervention\Image\Facades\Image;
use ComposeDe\Blogify\Requests\PostRequest;
use ComposeDe\Blogify\Models\Post;
use ComposeDe\Blogify\Services\BlogifyMailer;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Auth\Guard;

class PostsController extends BaseController
{

    /**
     * @var \ComposeDe\Blogify\Models\Post
     */
    protected $posts;

    /**
     * @var \ComposeDe\Blogify\Models\Status
     */
    protected $statuses;

    /**
     * @var \ComposeDe\Blogify\Models\Visibility
     */
    protected $visibilities;

    /**
     * @var \ComposeDe\Blogify\Models\Category
     */
    protected $categories;

    /**
     * @var \ComposeDe\Blogify\Models\Tag
     */
    protected $tags;

    protected $users;

    /**
     * @var \ComposeDe\Blogify\Models\Role
     */
    protected $roles;

    /**
     * Holds the post data
     *
     * @var object
     */
    protected $data;
    
    /**
     * @var \ComposeDe\Blogify\Services\BlogifyMailer
     */
    protected $mail;

    /**
     * @var \Illuminate\Contracts\Cache\Repository;
     */
    protected $cache;

    /**
     * @var \ComposeDe\Blogify\Blogify
     */
    protected $blogify;

    /**
     * @param \BlogifyTagModel|\ComposeDe\Blogify\Models\Tag               $tags
     * @param \BlogifyRoleModel|\ComposeDe\Blogify\Models\Role             $roles
     * @param \BlogifyPostModel|\ComposeDe\Blogify\Models\Post             $posts
     * @param \ComposeDe\Blogify\Services\BlogifyMailer                    $mail
     * @param \BlogifyStatusModel|\ComposeDe\Blogify\Models\Status         $statuses
     * @param \Illuminate\Contracts\Cache\Repository                       $cache
     * @param \BlogifyCategoryModel|\ComposeDe\Blogify\Models\Category     $categories
     * @param \BlogifyVisibilityModel|\ComposeDe\Blogify\Models\Visibility $visibilities
     * @param \Illuminate\Contracts\Auth\Guard                             $auth
     * @param \ComposeDe\Blogify\Blogify                                   $blogify
     */
    public function __construct(
        BlogifyTagModel $tags,
        BlogifyRoleModel $roles,
        BlogifyPostModel $posts,
        BlogifyMailer $mail,
        BlogifyStatusModel $statuses,
        Repository $cache,
        BlogifyCategoryModel $categories,
        BlogifyVisibilityModel $visibilities,
        Guard $auth,
        Blogify $blogify
    ) {
        parent::__construct($auth);

        $this->appendMiddleware();
        $this->users = app(config('blogify.models.user'));
        $this->tags = $tags;
        $this->roles = $roles;
        $this->posts = $posts;
        $this->mail = $mail;
        $this->cache = $cache;
        $this->statuses = $statuses;
        $this->blogify = $blogify;
        $this->categories = $categories;
        $this->visibilities = $visibilities;
    }

    ///////////////////////////////////////////////////////////////////////////
    // View methods
    ///////////////////////////////////////////////////////////////////////////

    /**
     * @param bool $trashed
     * @return \Illuminate\View\View
     */
    public function index($trashed = false)
    {
        $query = $this->posts
            ->with('status')
            ->forRole($this->users->getHighestRole()->name)
            ->orderBy('publish_date', 'DESC');
        if ($trashed) {
            $query->onlyTrashed();
        }
        $posts = $query->paginate($this->config->items_per_page);

        return view('blogify::admin.posts.index', compact('posts', 'trashed'));
    }

    public function create()
    {
        $id = $this->users->id;
        $post = $this->cache->has("autoSavedPost-$id") ? $this->buildPostObject() : null;
        $data = $this->getViewData($post);

        return view('blogify::admin.posts.form', $data);
    }

    public function show(Post $post)
    {
        return view('blogify::admin.posts.show', compact('post'));
    }

    public function edit(BlogifyPostModel $post)
    {
        $post->being_edited_by = $this->users->id;
        $post->save();

        $data = $this->getViewData($post);

        return view('blogify::admin.posts.form', $data);
    }

    public function store(PostRequest $request)
    {
        $formData = objectify($request->except([
            '_token', 'newCategory', 'newTags'
        ]));

        $post = $this->storeOrUpdatePost($formData);
        $post->assignTags($request->get('tags', []));
        $status = $this->statuses->byHash($request->get('status'));

        if ($status->name == Status::PENDING && $this->config->notify_reviewers) {
            $this->mailReviewer($post);
        }

        $action = $request->get('hash') == null ? 'created' : 'updated';
        $this->flashSuccess($post->title, $action);

        $this->cache->forget("autoSavedPost-{$this->users->id}");

        return redirect()->route('admin.posts.index');
    }

    /**
     * @param \BlogifyPostModel $post
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(BlogifyPostModel $post)
    {
        $postTitle = $post->title;
        $post->delete();

        $this->flashSuccess($postTitle, 'deleted');

        return redirect()->route('admin.posts.index');
    }

    /**
     * Function to upload images using
     * the SKEditor
     *
     * note: no CSRF protection on the route that is
     * calling this function because we are using the
     * CKEditor within an iframe :(
     *
     * @param \ComposeDe\Blogify\Requests\ImageUploadRequest $request
     * @return string
     */
    public function uploadImage(ImageUploadRequest $request)
    {
        $image_name = $this->resizeAndSaveImage($request->file('upload'));
        $path = config('app.url').'/uploads/posts/'.$image_name;
        $func = $request->get('CKEditorFuncNum');
        $result = "<script>window.parent.CKEDITOR.tools.callFunction($func, '$path', 'Image has been uploaded')</script>";

        return $result;
    }

    /**
     * Cancel changes in a post
     * and set being_edited_by
     * back to null
     *
     * @param string $hash
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel($hash = null)
    {
        if (! isset($hash)) {
            return redirect()->route('admin.posts.index');
        }

        $userId = $this->users->id;
        if ($this->cache->has("autoSavedPost-$userId")) {
            $this->cache->forget("autoSavedPost-$userId");
        }

        $post = $this->posts->byHash($hash);
        $post->being_edited_by = null;
        $post->save();

        $this->flashSuccess($post->title, 'canceled');

        return redirect()->route('admin.posts.index');
    }

    /**
     * @param string $hash
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($hash)
    {
        $post = $this->posts->withTrashed()->byHash($hash);
        $post->restore();

        $this->flashSuccess($post->title, 'restored');

        return redirect()->route('admin.posts.index');
    }

    ///////////////////////////////////////////////////////////////////////////
    // Helper methods
    ///////////////////////////////////////////////////////////////////////////

    /**
     * @return void
     */
    private function appendMiddleware()
    {
        $this->middleware('HasAdminOrAuthorRole', [
            'only' => ['create'],
        ]);

        $this->middleware('CanEditPost', [
            'only' => ['edit'],
        ]);

        $this->middleware('DenyIfBeingEdited', [
            'only' => ['edit'],
        ]);

        $this->middleware('CanViewPost', [
            'only' => ['edit', 'show'],
        ]);
    }

    /**
     * Get the default data for the
     * create and edit view
     *
     * @param $post
     * @return array
     */
    private function getViewData($post = null)
    {
        return [
            'reviewers'     => $this->users->reviewers()->get(),
            'statuses'      => $this->statuses->all(),
            'categories'    => $this->categories->all(),
            'visibility'    => $this->visibilities->all(),
            'publish_date'  => Carbon::now()->format('d-m-Y H:i'),
            'post'          => $post,
            'tags'          => Tag::all(),
        ];
    }

    /**
     * @param $image
     * @return string
     */
    private function resizeAndSaveImage($image)
    {
        $image_name = $this->createImageName();
        $fullpath = $this->createFullImagePath($image_name, $image->getClientOriginalExtension());

        Image::make($image->getRealPath())
            ->resize($this->config->image_sizes->posts[0], null, function($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->save($fullpath);

        return $image_name.'.'.$image->getClientOriginalExtension();
    }

    /**
     * @param string $image_name
     * @param $extension
     * @return string
     */
    private function createFullImagePath($image_name, $extension)
    {
        return public_path($this->config->upload_paths->posts->images.$image_name.'.'.$extension);
    }

    /**
     * @return string
     */
    private function createImageName()
    {
        return time().'-'.str_replace(' ', '-', $this->users->fullName);
    }

    /**
     * @return \ComposeDe\Blogify\Models\Post
     */
    private function storeOrUpdatePost($data)
    {
        if (! empty($data->hash)) {
            $post = $this->posts->byHash($data->hash);
        } else {
            $post = new Post;
            $post->hash = $this->blogify->makeHash(config('blogify.tables.posts'), 'hash', true);
        }

        $post->slug = $data->slug;
        $post->title = $data->title;
        $post->content = $data->post;
        $post->status_id = $this->statuses->byHash($data->status)->id;
        $post->publish_date = $data->publishdate;
        $post->user_id = $this->users->id;
        $post->reviewer_id = $data->reviewer;
        $post->visibility_id = $this->visibilities->byHash($data->visibility)->id;
        $post->category_id = $this->categories->byHash($data->category)->id;
        $post->being_edited_by = null;

        if (!empty($data->password)) {
            $post->password = bcrypt($data->password);
        }

        $post->save();

        return $post;
    }

    /**
     * @param \ComposeDe\Blogify\Models\Post $post
     * @return void
     */
    private function mailReviewer($post)
    {
        $reviewer = $this->users->find($post->reviewer_id);
        $data = [
            'reviewer'  => $reviewer,
            'post'      => $post,
        ];

        $this->mail->mailReviewer($reviewer->email, 'An article needs your expertise', $data);
    }

    /**
     * Build a post object when there
     * is a cached post so we can put
     * the data back in the form
     *
     * @return object
     */
    private function buildPostObject()
    {
        $cachedPost = $this->cache->get("autoSavedPost-{$this->users->id}");
        $category = $this->categories->byHash($cachedPost['category']);

        $post = new Post;
        $post->hash = '';
        $post->title = $cachedPost['title'];
        $post->slug = $cachedPost['slug'];
        $post->content = $cachedPost['content'];
        $post->publish_date = $cachedPost['publishdate'];
        $post->status_id = $this->statuses->byHash($cachedPost['status'])->id;
        $post->visibility_id = $this->visibilities->byHash($cachedPost['visibility'])->id;
        $post->reviewer_id = $this->users->find($cachedPost['reviewer'])->id;
        $post->category_id = $category ? $category->id : null;
        $post->assignTagsRelation($cachedPost['tags']);

        return $post;
    }

    protected function flashSuccess($name, $action, $model = '')
    {
        parent::flashSuccess($name, $action, 'Post');
    }
}
