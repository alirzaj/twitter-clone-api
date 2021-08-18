<?php

namespace App\Elasticsearch\Commands;

use Elasticsearch\Client;
use Illuminate\Console\Command;

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

            $this->client->indices()->create([
                'index' => $index->name,
                'body' => [
                    'mappings' => [
                        'properties' => array_map(fn($type) => ['type' => $type], $index->properties)
                    ]
                ]
            ]);

            $this->info("created $index->name");
        });

        return 0;
    }
}
