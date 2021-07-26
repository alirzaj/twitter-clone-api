<?php

namespace App\Http\Resources\Feed;

use App\Http\Resources\Tweet\ShowTweetUserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'user' => $this->whenLoaded('user', function () {
                return new ShowTweetUserResource($this->user);
            }),
            'parent_tweet_id' => $this->parent_tweet_id,
            'retweets_count' => $this->retweets_count,
            'replies_count' => $this->replies_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
