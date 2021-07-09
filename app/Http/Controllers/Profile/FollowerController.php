<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\OtherUserFollowersResource;
use App\Models\User;
use App\Repositories\UserRepository;

class FollowerController extends Controller
{
    public function index(UserRepository $userRepository, string $user)
    {
        $user = User::query()->select('id')->where('username', $user)->firstOrFail();

        return OtherUserFollowersResource::collection($userRepository->followers($user));
    }
}
