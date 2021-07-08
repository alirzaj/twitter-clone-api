<?php

namespace App\Http\Resources\Authentication;

use Illuminate\Http\Resources\Json\JsonResource;

class RegisterResource extends JsonResource
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
            'token' => $this->createToken($request->ip())->plainTextToken
        ];
    }
}
