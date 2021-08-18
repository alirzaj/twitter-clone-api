<?php

namespace App\Providers;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ElasticsearchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $client = ClientBuilder::create();

        if ($this->app->environment('local', 'testing')) {
            $client->setLogger(
                (new Logger('elasticsearch'))->pushHandler(
                    new StreamHandler(storage_path('logs/elastic.log'), Logger::DEBUG)
                )
            );
        }

        $this->app->singleton(Client::class, fn() => $client->build());
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
