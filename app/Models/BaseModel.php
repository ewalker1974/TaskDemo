<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BaseModel extends Model
{
    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($post): void {
            $post->uid = (string) Str::uuid();
        });
    }
}
