<?php

namespace ComposeDe\Blogify\Models;


class Status extends BaseModel
{
    const DRAFT = 'Draft';
    const PENDING = 'Pending review';
    const REVIEWED = 'Reviewed';

    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        $this->table = config('blogify.tables.statuses');
        parent::__construct($attributes);
    }

    public function post()
    {
        return $this->hasMany(config('blogify.models.post'));
    }

    public static function getDraftId()
    {
        return (new static)->getCachedId(static::DRAFT);
    }

    public static function getPendingId()
    {
        return (new static)->getCachedId(static::PENDING);
    }

    public static function getReviewedId()
    {
        return (new static)->getCachedId(static::REVIEWED);
    }

    public function getDraftStatusName()
    {
        return static::DRAFT;
    }

    public function getPendingStatusName()
    {
        return static::PENDING;
    }

    public function getReviewedStatusName()
    {
        return static::REVIEWED;
    }
}
