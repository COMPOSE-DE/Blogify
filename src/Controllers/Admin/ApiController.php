<?php

namespace ComposeDe\Blogify\Controllers\Admin;

use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use ComposeDe\Blogify\Exceptions\BlogifyException;
use BlogifyPostModel;
use BlogifyUserModel;
use Illuminate\Contracts\Cache\Repository as Cache;
use Carbon\Carbon;
use BlogifyTagModel;

class ApiController extends BaseController
{
    /**
     * Order the data of a given table on the given column
     * and the given order
     *
     * @param string $table
     * @param string $column
     * @param string $order
     * @param bool   $trashed
     *
     * @return object
     */
    public function sort(
        $table,
        $column,
        $order,
        $trashed = false
    ) {
        $tableName = config('blogify.tables.' . $table);

        if($table == 'users') {
            $query = BlogifyUserModel::with('roles');
        }
        elseif($table == 'posts') {
            $query = BlogifyPostModel::with('status');
        }

        if($trashed) {
            $query->onlyTrashed();
        }

        $query->orderBy($tableName . '.' . $column, $order);

        return $query->paginate($this->config->items_per_page);
    }

    /**
     * Check if a given slug already exists
     * and when it exists generate a new one
     *
     * @param string $slug
     * @return string
     */
    public function checkIfSlugIsUnique($slug, BlogifyPostModel $posts)
    {
        $i = 0;
        $baseSlug = $slug;

        while ($posts->whereSlug($slug)->get()->count() > 0) {
            $i++;
            $slug = "$baseSlug-$i";
        }

        return $slug;
    }

    /**
     * Save the current post in the cache
     *
     * @param \Illuminate\Contracts\Cache\Repository $cache
     * @param \Illuminate\Http\Request $request;
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function autoSave(Cache $cache, Request $request, BlogifyTagModel $tags)
    {
        try {
            $tags->findOrCreateTags($request->get('tags') ?: []);

            $id = $this->users->id;
            $cache->put(
                "autoSavedPost-$id",
                $request->all(),
                Carbon::now()->addHours(2)
            );
        } catch (BlogifyException $exception) {
            return response()->json([
                'saved' => false,
                'timestamp' => date('d-m-Y H:i:s')
            ]);
        }

        return response()->json([
            'saved' => true,
            'timestamp' => date('d-m-Y H:i:s')
        ]);
    }

    /**
     * @param \ComposeDe\Blogify\Models\Tag $tag
     * @return mixed
     */
    public function getTag(BlogifyTagModel $tag)
    {
        return $tag;
    }
}
