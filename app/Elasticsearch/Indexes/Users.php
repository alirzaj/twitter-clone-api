<?php

namespace App\Elasticsearch\Indexes;

use Illuminate\Database\Eloquent\Model;

class Users
{
    /**
     * the name of the index
     *
     * @var string
     */
    public $name = 'users';

    /**
     * properties (columns) and their types
     *
     * @var array|string[]
     */
    public array $properties = [
        'name' => 'text',
        'username' => 'keyword',
        'bio' => 'text',
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
            'name' => $model->name,
            'username' => $model->username,
            'bio' => $model->bio,
        ]);
    }
}
