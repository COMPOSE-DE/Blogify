<?php

namespace Donatix\Blogify\Controllers\Admin;

use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Donatix\Blogify\Exceptions\BlogifyException;
use Donatix\Blogify\Models\Post;
use Illuminate\Contracts\Cache\Repository as Cache;
use Carbon\Carbon;
use Donatix\Blogify\Models\Tag;

class ApiController extends BaseController
{
    /**
     * Order the data of a given table on the given column
     * and the given order
     *
     * @param string $table
     * @param string $column
     * @param string $order
     * @param bool $trashed
     * @param \Illuminate\Database\DatabaseManager $db
     * @return object
     */
    public function sort(
        $table,
        $column,
        $order,
        $trashed = false,
        DatabaseManager $db
    ) {
        $db = $db->connection();
        $data = $db->table($table);

        // Check for trashed data
        $data = $trashed ? $data->whereNotNull('deleted_at') : $data->whereNull('deleted_at');

        if ($table == 'users') {
            $data = $data->join('roles', 'users.role_id', '=', 'roles.id');
        }

        if ($table == 'posts') {
            $data = $data->join('statuses', 'posts.status_id', '=', 'statuses.id');
        }

        $data = $data
            ->orderBy($column, $order)
            ->paginate($this->config->items_per_page);

        return $data;
    }

    /**
     * Check if a given slug already exists
     * and when it exists generate a new one
     *
     * @param string $slug
     * @return string
     */
    public function checkIfSlugIsUnique($slug)
    {
        $i = 0;
        $baseSlug = $slug;

        while (Post::whereSlug($slug)->get()->count() > 0) {
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
    public function autoSave(Cache $cache, Request $request)
    {
        try {
            Tag::findOrCreateTags($request->get('tags', []));

            $id = $this->user->id;
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
     * @param \Donatix\Blogify\Models\Tag $tag
     * @return mixed
     */
    public function getTag(Tag $tag)
    {
        return $tag;
    }
}
