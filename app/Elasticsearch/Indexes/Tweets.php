<?php

namespace App\Elasticsearch\Indexes;

use Illuminate\Database\Eloquent\Model;

class Tweets
{
    /**
     * the name of the index
     *
     * @var string
     */
    public $name = 'tweets';

    /**
     * properties (columns) and their types
     *
     * @var array|string[]
     */
    public array $properties = [
        'text' => 'text',
        'user_id' => 'keyword',
        'parent_tweet_id' => 'keyword',
        'user_ip' => 'ip',
        'created_at' => 'date',
        'likes_count' => 'unsigned_long',
        'impressions_count' => 'unsigned_long',
        'retweets_count' => 'unsigned_long',
        'replies_count' => 'unsigned_long',
    ];

    /**
     * return an array of model ready to be indexed
     *
     * @param Model $model
     * @return array
     */
    public function toArray(Model $model): array
    {
       return array_filter([
           'id' => $model->id,
           'text' => $model->text,
           'user_id' => $model->user_id,
           'parent_tweet_id' => $model->parent_tweet_id,
           'user_ip' => request()->ip(),
           'created_at' => $model->created_at,
           'likes_count' => $model->likes_count,
           'impressions_count' => $model->impressions_count,
           'retweets_count' => $model->retweets_count,
           'replies_count' => $model->replies_count,
       ]);
    }
}
