<?php

namespace App\Http\Controllers\Follow;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\OtherUserFollowingsResource;
use App\Models\User;
use App\Repositories\UserRepository;

class FollowingController extends Controller
{
    public function index(UserRepository $userRepository, string $user)
    {
        $user = User::query()->select('id')->where('username', $user)->firstOrFail();

        return OtherUserFollowingsResource::collection($userRepository->followings($user));
    }
}
