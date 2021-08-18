<?php

namespace App\Elasticsearch\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

trait ReadsIndexFile
{
    private function indexes(): Collection
    {
        $migrations = $this
            ->filesystem()
            ->files(app_path('Elasticsearch' . DIRECTORY_SEPARATOR . 'Indexes'));

        return collect($migrations)->map(
            fn($migration) => new ("App\\ElasticSearch\\Indexes\\" . $migration->getFilenameWithoutExtension())
        );
    }

    private function fileSystem(): Filesystem
    {
        return resolve(Filesystem::class);
    }
}
