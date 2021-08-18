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
    protected $signature = 'elastic:delete-index {name?}';

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
        $this->hasOption('name') ?
            $this->deleteIndex($this->option('name')) :
            $this->indexes()->each(fn($index) => $this->deleteIndex($index->name));

        return 0;
    }

    private function deleteIndex(string $name): void
    {
        $this->client->indices()->delete(['index' => $name]);

        $this->info("$name deleted");
    }
}
