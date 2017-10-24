<?php

namespace ComposeDe\Blogify\Controllers\Admin;

use ComposeDe\Blogify\Models\Tag;
use Illuminate\Http\Request;
use ComposeDe\Blogify\Requests\TagUpdateRequest;
use BlogifyTagModel;

class TagsController extends BaseController
{

    public function index($trashed = null, BlogifyTagModel $tags)
    {
        $q = $tags->orderBy('created_at', 'DESC');
        if ($trashed) {
            $q->onlyTrashed();
        }

        $tags = $q->paginate($this->config->items_per_page);

        return view('blogify::admin.tags.index', compact('tags', 'trashed'));
    }

    public function create()
    {
        return view('blogify::admin.tags.form');
    }

    public function edit(Tag $tag)
    {
        return view('blogify::admin.tags.form', compact('tag'));
    }

    public function storeOrUpdate(Request $request, BlogifyTagModel $tagsModel)
    {
        $tags = collect(explode(',', $request->get('tags')))->map(function ($tag) {
            return trim($tag);
        });

        $request->replace(['tags' => $tags->all()]);
        $this->validate($request, [
            'tags' => 'required|array',
            'tags.*' => 'required|min:2|max:45'
        ]);

        $storedTags = $tagsModel->createMissing($tags);

        $this->flashSuccess($storedTags->pluck('name')->implode(', '), 'created');

        return redirect()->route('admin.tags.index');
    }

    /**
     * @param \BlogifyTagModel                             $tag
     * @param \ComposeDe\Blogify\Requests\TagUpdateRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(BlogifyTagModel $tag, TagUpdateRequest $request)
    {
        $tag->name = $request->tags;
        $tag->save();

        $this->flashSuccess($tag->name, 'updated');

        return redirect()->route('admin.tags.index');
    }

    public function destroy(BlogifyTagModel $tag)
    {
        $tagName = $tag->name;
        $tag->delete();

        $this->flashSuccess($tagName, 'deleted');

        return redirect()->route('admin.tags.index');
    }

    /**
     * @param string $hash
     * @param BlogifyTagModel                              $tag
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($hash, BlogifyTagModel $tags)
    {
        $tag = $tags->withTrashed()->byHash($hash);
        $tag->restore();

        $this->flashSuccess($tag->name, 'restored');

        return redirect()->route('admin.tags.index');
    }

    protected function flashSuccess($name, $action, $model = '')
    {
        parent::flashSuccess($name, $action, 'Tag');
    }
}
