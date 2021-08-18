<?php

namespace App\Elasticsearch\Commands;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CreateIndex extends Command
{
    use ReadsIndexFile;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elastic:create-index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create all defined indexes in elasticsearch';

    private Client $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        parent::__construct();

        $this->client = $client;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->indexes()->each(function ($index) {
            $this->info("creating $index->name");

            try {
                $this->createIndex($index);
            } catch (BadRequest400Exception) {
                $this->alert("$index->name already exists. deleting it...");

                Artisan::call('elastic:delete-index', ['name' => $index->name]);

                $this->createIndex($index);
            }

            $this->info("created $index->name");
        });

        return 0;
    }

    private function createIndex($index): void
    {
        $this->client->indices()->create([
            'index' => $index->name,
            'body' => [
                'mappings' => [
                    'properties' => array_map(fn($type) => ['type' => $type], $index->properties)
                ]
            ]
        ]);
    }
}
