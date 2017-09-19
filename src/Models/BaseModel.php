<?php

namespace Donatix\Blogify\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $hasHash = true;
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->generateHash($model->getTable());
        });
    }

    public function generateHash($table)
    {
        if (! $this->hash && $this->hasHash) {
            $this->hash = app('Donatix.blogify')->makeHash($table, 'hash', true);
        }
    }

    public function scopeByHash($query, $hash)
    {
        return $query->whereHash($hash)->first();
    }

    public function getCreatedAtAttribute($value)
    {
        return date("d-m-Y H:i", strtotime($value));
    }
}
