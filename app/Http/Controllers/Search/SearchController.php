<?php

namespace App\Http\Controllers\Search;

use App\Elasticsearch\Indexes\Users;
use App\Http\Controllers\Controller;
use App\Http\Requests\Search\HashtagRequest;
use App\Http\Requests\Search\SearchRequest;
use App\Http\Resources\Tweet\ShowTweetResource;
use App\Http\Resources\User\UserSearchResultResource;
use App\Models\Tweet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SearchController extends Controller
{
    public function show(SearchRequest $request)
    {
        $results = Tweet::elasticsearchQuery()
            ->addIndex(Users::class)
            ->multiMatch(['text', 'name', 'username', 'bio^0.3'], $request->input('q'))
            ->hydrate()
            ->groupBy(function (Model $model) {
                return strtolower(class_basename($model));
            })
            ->each(fn(Collection $items, string $key) => $items
                ->when($key === 'user')
                ->load(['media' => fn(MorphMany $query) => $query
                    ->where('collection_name', 'avatar')
                ])
                ->when($key === 'tweet')
                ->load(['user' => fn(BelongsTo $query) => $query
                    ->select('id', 'name', 'username')
                    ->with(['media' => fn(MorphMany $query) => $query
                        ->where('collection_name', 'avatar')
                    ])
                ])
            );

        return [
            'users' => UserSearchResultResource::collection($results['user'] ?? []),
            'tweets' => ShowTweetResource::collection($results['tweet'] ?? []),
        ];
    }

    public function hashtag(HashtagRequest $request)
    {
        return ShowTweetResource::collection(Tweet::elasticsearchQuery()
            ->match('text.hashtags', $request->input('hashtag'), 'hashtag', 0)
            ->hydrate()
            ->load(['user' => fn(BelongsTo $query) => $query
                ->select('id', 'name', 'username')
                ->with(['media' => fn(MorphMany $query) => $query
                    ->where('collection_name', 'avatar')
                ])
            ]));
    }
}
