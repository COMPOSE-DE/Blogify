<?php

namespace ComposeDe\Blogify\Models;

use BlogifyAuth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use BlogifyAuthModel;
use BlogifyCommentModel;
use BlogifyCategoryModel;
use BlogifyMediaModel;
use BlogifyTagModel;
use BlogifyStatusModel;
use BlogifyVisibilityModel;

class Post extends BaseModel
{
    use SoftDeletes;

    public function __construct(array $attributes = [])
    {
        $this->table = config('blogify.tables.posts');
        parent::__construct($attributes);
    }

    public function getRouteKeyName()
    {
        return 'hash';
    }

    public function user()
    {
        return $this->belongsTo(BlogifyAuthModel::class)->withTrashed();
    }

    public function comment()
    {
        return $this->hasMany(BlogifyCommentModel::class);
    }

    public function category()
    {
        return $this->belongsTo(BlogifyCategoryModel::class)->withTrashed();
    }

    public function media()
    {
        return $this->hasMany(BlogifyMediaModel::class);
    }

    public function tags()
    {
        return $this->belongsToMany(BlogifyTagModel::class, config('blogify.tables.post_tag'), 'post_id', 'tag_id')->withTrashed();
    }

    public function status()
    {
        return $this->belongsTo(BlogifyStatusModel::class);
    }

    public function visibility()
    {
        return $this->belongsTo(BlogifyVisibilityModel::class);
    }

    public function approvedComments()
    {
        return $this->hasMany(BlogifyCommentModel::class)->approved();
    }

    public function comments()
    {
        return $this->hasMany(BlogifyCommentModel::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    |
    | For more information pleas check out the official Laravel docs at
    | http://laravel.com/docs/5.0/eloquent#accessors-and-mutators
    |
    */

    public function getCommentsCountAttribute()
    {
        return $this->approvedComments->count();
    }

    public function setPublishDateAttribute($value)
    {
        $this->attributes['publish_date'] = date("Y-m-d H:i:s", strtotime($value));
    }

    public function getPublishDateAttribute($value)
    {
        return date("d-m-Y H:i", strtotime($value));
    }

    public function getAuthorNameAttribute()
    {
        return $this->user->name;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | For more information pleas check out the official Laravel docs at
    | http://laravel.com/docs/5.0/eloquent#query-scopes
    |
    */

    public function scopeForRole($query, $role)
    {
        $roleModel = app(config('blogify.models.role'));

        if ($role === $roleModel->getAdminRoleName()) {
            return $query;
        }

        if ($role === $roleModel->getAuthorRoleName()) {
            return $query->whereReviewerId(BlogifyAuth::id());
        }

        if ($role === $roleModel->getMemberRoleName()) {
            return $query->whereUserId(BlogifyAuth::id());
        }
    }

    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    public function scopeForPublic($query)
    {
        return $query->where('publish_date', '<=', date('Y-m-d H:i:s'))
                    ->whereIn('visibility_id', app(config('blogify.models.visibility'))->getPublicIds());
    }

    public function scopeRecommended($query)
    {
        return $query->where('visibility_id', app(config('blogify.models.visibility'))->getRecommendedId());
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'DESC');
    }

    public function hasTag($tagToCheck)
    {
        return $this->tags->contains(function($tag) use ($tagToCheck) {
            return $tag->name === $tagToCheck->name;
        });
    }

    public function preview()
    {
        $this->increment('views_count');
    }

    public function assignTags($tags)
    {
        $tags = app(BlogifyTagModel::class)->findOrCreateTags($tags);

        $this->tags()->sync($tags->pluck('id'));
    }

    public function assignTagsRelation($tags = [])
    {
        $this->setRelation('tags', (new Collection($tags))->map(function($tag) {
            return app(BlogifyTagModel::class)->make(['name' => $tag]);
        }));
    }

    public function hasPassword()
    {
        return $this->visibility_id === app(BlogifyVisibilityModel::class)->getProtectedId();
    }

    public function passwordIs($password)
    {
        return Hash::check($password, $this->password);
    }
}
