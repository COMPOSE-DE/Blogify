<?php

namespace Donatix\Blogify\Models;

use Auth;
use Donatix\Blogify\Models\Tag;
use Donatix\Blogify\Models\Media;
use Donatix\Blogify\Models\Status;
use Donatix\Blogify\Models\Comment;
use Donatix\Blogify\Models\Category;
use Donatix\Blogify\Models\Visibility;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Collection;

class Post extends BaseModel
{

    use SoftDeletes;

    /**
     * The database table used by the model
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Set or unset the timestamps for the model
     *
     * @var bool
     */
    public $timestamps = true;

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | For more information pleas check out the official Laravel docs at
    | http://laravel.com/docs/5.0/eloquent#relationships
    |
    */

   public function getRouteKeyName()
   {
       return 'hash';
   }

    public function user()
    {
        return $this->belongsTo(config('blogify.auth_model'))->withTrashed();
    }

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'posts_have_tags', 'post_id', 'tag_id')->withTrashed();
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function visibility()
    {
        return $this->belongsTo(Visibility::class);
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

    public function setPublishDateAttribute($value)
    {
        $this->attributes['publish_date'] = date("Y-m-d H:i:s", strtotime($value));
    }

    public function getPublishDateAttribute($value)
    {
        return date("d-m-Y H:i", strtotime($value));
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

    public function scopeForAdmin($query)
    {
        return $query;
    }

    public function scopeForReviewer($query)
    {
        return $query->whereReviewerId(Auth::user()->id);
    }

    public function scopeForAuthor($query)
    {
        return $query->whereUserId(Auth::user()->id);
    }

    public function scopeBySlug($query, $slug)
    {
        return $query->whereSlug($slug)->first();
    }

    public function scopeForPublic($query)
    {
        return $query->where('publish_date', '<=', date('Y-m-d H:i:s'))
                    ->whereIn('visibility_id', Visibility::getPublicIds());
    }

    public function scopeRecommended($query)
    {
        return $query->where('visibility_id', Visibility::getRecommendedId());
    }

    public function hasTag($tagToCheck)
    {
        return $this->tags->contains(function($tag) use ($tagToCheck) {
            return $tag->name === $tagToCheck->name;
        });
    }

    public function assignTags($tags)
    {
        $tags = Tag::findOrCreateTags($tags);

        $this->tags()->sync($tags->pluck('id'));
    }

    public function assignTagsRelation($tags = [])
    {
        $this->setRelation('tags', (new Collection($tags))->map(function($tag) {
            return Tag::make(['name' => $tag]);
        }));
    }
}
