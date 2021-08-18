<?php

use App\Elasticsearch\Commands\ReadsIndexFile;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Illuminate\Support\Facades\Artisan;

uses(ReadsIndexFile::class);

beforeEach(function () {
    $this->client = resolve(Client::class);

    Artisan::call('elastic:create-index');
});


it('can create indexes', function () {
    $this->indexes()->each(function ($index) {
        $mappings = $this
            ->client
            ->indices()
            ->getMapping(['index' => $index->name])[$index->name]['mappings']['properties'];

        foreach ($index->properties as $field => $type) {
            expect($mappings)->toHaveKey($field);
            expect($mappings[$field])->toMatchArray(['type' => $type]);
        }
    });

    Artisan::call('elastic:delete-index');
});


it('can delete indexes', function () {
    Artisan::call('elastic:delete-index');

    $this->indexes()->each(function ($index) {
        $this
            ->client
            ->indices()
            ->getMapping(['index' => $index->name]);
    });
})->throws(Missing404Exception::class);
