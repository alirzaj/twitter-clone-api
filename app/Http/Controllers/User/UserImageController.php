<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserImageRequest;

class UserImageController extends Controller
{
    public function store(StoreUserImageRequest $request)
    {
        auth()
            ->user()
            ->addMediaFromRequest('image')
            ->usingFileName($request->file('image')->hashName())
            ->toMediaCollection($request->input('collection'));

        return response()->json([], 204);
    }
}
