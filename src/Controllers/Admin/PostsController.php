<?php

namespace Donatix\Blogify\Controllers\Admin;

use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Hashing\Hasher;
use Donatix\Blogify\Blogify;
use Donatix\Blogify\Models\Category;
use Donatix\Blogify\Models\Role;
use Donatix\Blogify\Models\Status;
use Donatix\Blogify\Models\Tag;
use Donatix\Blogify\Models\Visibility;
use Donatix\Blogify\Requests\ImageUploadRequest;
use Intervention\Image\Facades\Image;
use Donatix\Blogify\Requests\PostRequest;
use Donatix\Blogify\Models\Post;
use Donatix\Blogify\Services\BlogifyMailer;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Auth\Guard;

class PostsController extends BaseController
{

    /**
     * @var \Donatix\Blogify\Models\Post
     */
    protected $post;

    /**
     * @var \Donatix\Blogify\Models\Status
     */
    protected $status;

    /**
     * @var \Donatix\Blogify\Models\Visibility
     */
    protected $visibility;

    /**
     * @var \Donatix\Blogify\Models\Category
     */
    protected $category;

    /**
     * @var \Donatix\Blogify\Models\Tag
     */
    protected $tag;

    /**
     * @var \Donatix\Blogify\Models\Role
     */
    protected $role;

    /**
     * Holds the post data
     *
     * @var object
     */
    protected $data;

    /**
     * Holds all the tags that are
     * assigned to a post
     *
     * @var array
     */
    protected $tags = [];

    /**
     * @var \Donatix\Blogify\Services\BlogifyMailer
     */
    protected $mail;

    /**
     * @var \Illuminate\Contracts\Cache\Repository;
     */
    protected $cache;

    /**
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hash;

    /**
     * @var \Donatix\Blogify\Blogify
     */
    protected $blogify;

    /**
     * @param \Donatix\Blogify\Models\Tag $tag
     * @param \Donatix\Blogify\Models\Role $role
     * @param \App\User $user
     * @param \Donatix\Blogify\Models\Post $post
     * @param \Donatix\Blogify\Services\BlogifyMailer $mail
     * @param \Illuminate\Contracts\Hashing\Hasher $hash
     * @param \Donatix\Blogify\Models\Status $status
     * @param \Illuminate\Contracts\Cache\Repository $cache
     * @param \Donatix\Blogify\Models\Category $category
     * @param \Donatix\Blogify\Models\Visibility $visibility
     * @param \Illuminate\Contracts\Auth\Guard $auth
     * @param \Donatix\Blogify\Blogify $blogify
     */
    public function __construct(
        Tag $tag,
        Role $role,
        Post $post,
        BlogifyMailer $mail,
        Hasher $hash,
        Status $status,
        Repository $cache,
        Category $category,
        Visibility $visibility,
        Guard $auth,
        Blogify $blogify
    ) {
        parent::__construct($auth);

        $this->appendMiddleware();

        $this->tag = $tag;
        $this->role = $role;
        $this->post = $post;
        $this->mail = $mail;
        $this->hash = $hash;
        $this->cache = $cache;
        $this->status = $status;
        $this->blogify = $blogify;
        $this->category = $category;
        $this->visibility = $visibility;
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
        $scope = 'for'.$this->user->role->name;
        $data = [
            'posts' => (! $trashed) ?
                $this->post->$scope()
                        ->orderBy('publish_date', 'DESC')
                        ->paginate($this->config->items_per_page)
                :
                $this->post->$scope()
                        ->onlyTrashed()
                        ->orderBy('publish_date', 'DESC')
                        ->paginate($this->config->items_per_page),
            'trashed' => $trashed,
        ];

        return view('blogify::admin.posts.index', $data);
    }

    public function create()
    {
        $id = $this->user->id;
        $post = $this->cache->has("autoSavedPost-$id") ? $this->buildPostObject() : null;
        $data = $this->getViewData($post);

        return view('blogify::admin.posts.form', $data);
    }

    public function show(Post $post)
    {
        return view('blogify::admin.posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $post->being_edited_by = $this->user->id;
        $post->save();

        $cachedPost = $this->cache->has("autoSavedPost-{$this->user->id}") ? $this->buildPostObject() : $post;
        $data = $this->getViewData($cachedPost);

        return view('blogify::admin.posts.form', $data);
    }

    public function store(PostRequest $request)
    {
        $this->data = objectify($request->except([
            '_token', 'newCategory', 'newTags'
        ]));

        if (! empty($this->data->tags)) {
            $this->buildTagsArray();
        }

        $post = $this->storeOrUpdatePost();

        if ($this->status->byHash($this->data->status)->name == 'Pending review') {
            $this->mailReviewer($post);
        }

        $action = $request->hash == '' ? 'created' : 'updated';
        $this->flashSuccess($post->title, $action);

        $userId = $this->user->id;
        $this->cache->forget("autoSavedPost-{$userId}");

        return redirect()->route('admin.posts.index');
    }

    /**
     * @param string $hash
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Post $post)
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
     * @param \Donatix\Blogify\Requests\ImageUploadRequest $request
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

        $userId = $this->user->id;
        if ($this->cache->has("autoSavedPost-$userId")) {
            $this->cache->forget("autoSavedPost-$userId");
        }

        $post = $this->post->byHash($hash);
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
        $post = $this->post->withTrashed()->byHash($hash);
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
            'reviewers'     => User::reviewers()->get(),
            'statuses'      => $this->status->all(),
            'categories'    => $this->category->all(),
            'visibility'    => $this->visibility->all(),
            'publish_date'  => Carbon::now()->format('d-m-Y H:i'),
            'post'          => $post,
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
        return time().'-'.str_replace(' ', '-', $this->user->fullName);
    }

    /**
     * @return void
     */
    private function buildTagsArray()
    {
        $tags = explode(',', $this->data->tags);
        foreach ($tags as $hash) {
            array_push($this->tags, $this->tag->byHash($hash)->id);
        }
    }

    /**
     * @return \Donatix\Blogify\Models\Post
     */
    private function storeOrUpdatePost()
    {
        if (! empty($this->data->hash)) {
            $post = $this->post->byHash($this->data->hash);
        } else {
            $post = new Post;
            $post->hash = $this->blogify->makeHash('posts', 'hash', true);
        }

        $post->slug = $this->data->slug;
        $post->title = $this->data->title;
        $post->content = $this->data->post;
        $post->status_id = $this->status->byHash($this->data->status)->id;
        $post->publish_date = $this->data->publishdate;
        $post->user_id = $this->user->id;
        $post->reviewer_id = $this->data->reviewer;
        $post->visibility_id = $this->visibility->byHash($this->data->visibility)->id;
        $post->category_id = $this->category->byHash($this->data->category)->id;
        $post->being_edited_by = null;

        if (!empty($this->data->password)) {
            $post->password = bcrypt($this->data->password);
        }

        $post->save();
        $post->tags()->sync($this->tags);

        return $post;
    }

    /**
     * @param \Donatix\Blogify\Models\Post $post
     * @return void
     */
    private function mailReviewer($post)
    {
        $reviewer = $this->user->find($post->reviewer_id);
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
        $hash = $this->user->hash;
        $cached_post = $this->cache->get("autoSavedPost-$hash");

        $post = [];
        $post['hash'] = '';
        $post['title'] = $cached_post['title'];
        $post['slug'] = $cached_post['slug'];
        $post['content'] = $cached_post['content'];
        $post['publish_date'] = $cached_post['publishdate'];
        $post['status_id'] = $this->status->byHash($cached_post['status'])->id;
        $post['visibility_id'] = $this->visibility->byHash($cached_post['visibility'])->id;
        $post['reviewer_id'] = $this->user->find($cached_post['reviewer'])->id;
        $post['category_id'] = $this->category->byHash($cached_post['category'])->id;
        $post['tag'] = $this->buildTagsArrayForPostObject($cached_post['tags']);

        return objectify($post);
    }

    /**
     * @param $tags
     * @return array
     */
    private function buildTagsArrayForPostObject($tags)
    {
        if ($tags == "") {
            return [];
        }

        $aTags = [];
        $hashes = explode(',', $tags);

        foreach ($hashes as $tag) {
            array_push($aTags, $this->tag->byHash($tag));
        }

        return $aTags;
    }

    protected function flashSuccess($name, $action, $model = '')
    {
        parent::flashSuccess($name, $action, 'Post');
    }
}
