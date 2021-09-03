<?php

namespace App\Elasticsearch\Query;

use Closure;
use Elasticsearch\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Query
{
    private array $params = [
        'body' => []
    ];

    private array $compounds = [
        Should::class
    ];

    protected array $query;

    public function addIndex(string $index): Query
    {
        $this->params['index'][] = (new $index)->name;

        return $this;
    }

    public function boolean(Closure ...$queries): Query
    {
        foreach ($queries as $query) {
            $result = app()->call($query);

            $this->add('bool', $result->toArray());
        }

        return $this;
    }

    public function match(
        string $field,
        string|int|float $value,
        string $analyzer = null,
        string $fuzziness = 'AUTO'
    ): Query
    {
        $this->add('match', [
            $field => array_filter(['analyzer' => $analyzer, 'query' => $value, 'fuzziness' => $fuzziness])
        ]);

        return $this;
    }

    public function multiMatch(
        array $fields,
        string|int|float $value,
        string $analyzer = null,
        string $fuzziness = 'AUTO',
        string $type = 'best_fields'
    ): Query
    {
        $this->add(
            'multi_match',
            array_filter([
                'analyzer' => $analyzer,
                'query' => $value,
                'fuzziness' => $fuzziness,
                'type' => $type,
                'fields' => $fields
            ]));

        return $this;
    }

    private function add(string $name, array $query): void
    {
        in_array($this::class, $this->compounds) ?
            $this->query[][$name] = $query :
            $this->query[$name] = $query;
    }

    public function get(): Collection
    {
        return collect($this->executeQuery()['hits']['hits'])->pluck('_source');
    }

    public function hydrate(): \Illuminate\Database\Eloquent\Collection
    {
        return \Illuminate\Database\Eloquent\Collection::make($this->executeQuery()['hits']['hits'])
            ->map(fn(array $hit) => $this->getModel($hit['_index'])->forceFill($hit['_source']));
    }

    private function getModel(string $index): Model
    {
        $model = new (
        array_flip(config('elasticsearch.indices'))['App\\Elasticsearch\\Indexes\\' . Str::ucfirst($index)]
        );

        $model->exists = true;
        return $model;
    }

    private function executeQuery(): array
    {
        $this->params['body']['query'] = $this->query;

        return resolve(Client::class)->search($this->params);
    }

    public function dd(): void
    {
        dd($this->query);
    }

    public function dump(): void
    {
        dump($this->query);
    }
}
