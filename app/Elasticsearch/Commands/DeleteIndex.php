<?php

namespace App\Elasticsearch\Commands;

use Elasticsearch\Client;
use Illuminate\Console\Command;

class DeleteIndex extends Command
{
    use ReadsIndexFile;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elastic:delete-index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete all defined indexes in elasticsearch';

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
        $this->indexes()->each(
            fn($index) => $this->client->indices()->delete(['index' => $index->name])
        );

        return 0;
    }
}
