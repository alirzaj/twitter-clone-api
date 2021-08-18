<?php

namespace App\Elasticsearch;

use App\Elasticsearch\Jobs\IndexDocument;
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
            $index = new $model->index;

            IndexDocument::dispatch($index->name, $index->toArray($model));
        });
    }
}
