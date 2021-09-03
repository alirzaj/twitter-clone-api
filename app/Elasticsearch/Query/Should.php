<?php

namespace App\Elasticsearch\Query;

class Should extends Query
{
    public function toArray()
    {
        return [
            'should' => $this->query,
        ];
    }
}
