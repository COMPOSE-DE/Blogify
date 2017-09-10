<?php

namespace Donatix\Blogify\Controllers\Admin;

use Donatix\Blogify\Blogify;
use Donatix\Blogify\Models\Tag;
use Illuminate\Http\Request;
use Donatix\Blogify\Requests\TagUpdateRequest;
use jorenvanhocht\Tracert\Tracert;

class TagsController extends BaseController
{

    public function index($trashed = null)
    {
        $q = Tag::orderBy('created_at', 'DESC');
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

    public function storeOrUpdate(Request $request)
    {
        $tags = collect(explode(',', $request->get('tags')))->map(function ($tag) {
            return trim($tag);
        });

        $request->replace(['tags' => $tags->all()]);
        $this->validate($request, [
            'tags' => 'required|array',
            'tags.*' => 'required|min:2|max:45'
        ]);

        $storedTags = Tag::fromArray($tags);

        if ($request->wantsJson()) {
            return response()->json(['passed' => true, 'tags' => $storedTags], 201);
        }

        $this->flashSuccess($tags->implode(','), 'created');

        return redirect()->route('admin.tags.index');
    }

    /**
     * @param string $hash
     * @param \Donatix\Blogify\Requests\TagUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Tag $tag, TagUpdateRequest $request)
    {
        $tag->name = $request->tags;
        $tag->save();

        $this->flashSuccess($tag->name, 'updated');

        return redirect()->route('admin.tags.index');
    }

    public function destroy(Tag $tag)
    {
        $tagName = $tag->name;
        $tag->delete();

        $this->flashSuccess($tagName, 'deleted');

        return redirect()->route('admin.tags.index');
    }

    /**
     * @param string $hash
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($hash)
    {
        $tag = Tag::withTrashed()->byHash($hash);
        $tag->restore();

        $this->flashSuccess($tag->name, 'restored');

        return redirect()->route('admin.tags.index');
    }

    protected function flashSuccess($name, $action, $model = '')
    {
        parent::flashSuccess($name, $action, 'Tag');
    }
}
