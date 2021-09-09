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
    ];

    public array $fields = [
        'text' => [
            'hashtags' => [
                'type' => 'text',
                'analyzer' => 'hashtag'
            ]
        ]
    ];

    /**
     * define and config analyzers for index
     *
     * @var array|\string[][]
     */
    public array $analyzers = [
        'hashtag' => [
            'type' => 'custom',
            'tokenizer' => 'hashtag_tokenizer',
            'filter' => ['lowercase']
        ]
    ];

    /**
     * define & config tokenizers for index
     *
     * @var array
     */
    public array $tokenizers = [
        'hashtag_tokenizer' => [
            'type' => 'pattern',
            'pattern' => '#\S+',
            'group' => 0
        ]
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
            'likes_count' => $model->likes_count,
            'retweets_count' => $model->retweets_count,
            'replies_count' => $model->replies_count,
            'created_at' => $model->created_at,
        ]);
    }
}
