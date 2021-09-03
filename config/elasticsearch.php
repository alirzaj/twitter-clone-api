<?php

use App\Elasticsearch\Indexes\Tweets;
use App\Elasticsearch\Indexes\Users;
use App\Models\Tweet;
use App\Models\User;

return [
    'host' => '127.0.0.1:9200',
    'queue' => 'elasticsearch',

    'indices' => [
        User::class => Users::class,
        Tweet::class => Tweets::class
    ]
];
