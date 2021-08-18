<?php

namespace App\Elasticsearch\Indexes;

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
}
