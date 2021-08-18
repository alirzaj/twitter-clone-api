<?php

namespace App\Elasticsearch\Jobs;

use Elasticsearch\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IndexDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $name;
    private array $document;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $name, array $document)
    {
        $this->name = $name;
        $this->document = $document;

        $this->onQueue(config('elasticsearch.queue'));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Client $client)
    {
        $client->index([
            'index' => $this->name,
            'id' => $this->document['id'],
            'body' => $this->document,
            'refresh' => true
        ]);
    }
}
