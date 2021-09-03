<?php

namespace App\Elasticsearch;

use App\Elasticsearch\Jobs\IndexDocument;
use App\Elasticsearch\Query\Query;
use Illuminate\Database\Eloquent\Model;

trait Searchable
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    public static function bootSearchable()
    {
        static::created(function (Model $model) {
            $index = new (config('elasticsearch.indices')[$model::class]);

            IndexDocument::dispatch($index->name, $index->toArray($model));
        });
    }

    public static function elasticsearchQuery()
    {
        return (new Query())->addIndex(config('elasticsearch.indices.' . self::class));
    }
}
