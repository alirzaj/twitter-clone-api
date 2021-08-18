<?php

namespace Tests;

use Elasticsearch\Client;

trait InteractsWithElasticsearch
{
    public function assertElasticsearchHas(string $indexName, array $document)
    {
        if (empty($document['id'])){
           //todo without id
            expect(false)->toBeTrue();
        }

        expect($this->client()->get(['index' => $indexName,'id' => $document['id']])['_source'])
        ->toMatchArray(array_filter($document));

    }

    private function client(): Client
    {
        return resolve(Client::class);
    }
}
