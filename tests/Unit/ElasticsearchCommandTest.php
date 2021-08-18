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

it('can delete a specific index', function () {
    //todo better test when there is multiple index? maybe we must put multiple index just for this test?
    Artisan::call('elastic:delete-index', ['name' => $this->indexes()[0]->name]);

    $this->indexes()->each(function ($index) {
        $this
            ->client
            ->indices()
            ->getMapping(['index' => $index->name]);
    });
})->throws(Missing404Exception::class);

it('will recreate index when it exists and we want to create it', function () {
    //todo how to test it?
});
