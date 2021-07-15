<?php

namespace App\Http\Resources\Tweet;

use Illuminate\Http\Resources\Json\JsonResource;

class ShowTweetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'likes' => $this->likes,
            'impressions_count' => $this->impressions_count,
            'retweets_count' => $this->retweets_count,
            'replies_count' => $this->replies_count,
            'user' => $this->whenLoaded('user', function () {
                return new ShowTweetUserResource($this->user);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
