<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tweet extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]|bool
     */
    protected $guarded = ['id', 'likes', 'impressions', 'retweets'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function reply(): HasMany
    {
        return $this->hasMany(Tweet::class, 'parent_tweet_id', 'id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Tweet::class, 'parent_tweet_id', 'id');
    }
}
