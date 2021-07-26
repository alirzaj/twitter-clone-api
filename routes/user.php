<?php

use App\Http\Controllers\Follow\FollowController;
use App\Http\Controllers\Follow\FollowerController;
use App\Http\Controllers\Follow\FollowingController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserImageController;
use Illuminate\Support\Facades\Route;

//actions that need authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('users')->name('users.')->group(function () {
        //update the authenticated user's profile
        Route::patch('', [UserController::class, 'update'])->name('update');
        //work with images
        Route::prefix('images')->name('images.')->group(function () {
            //add avatar or wall
            Route::post('', [UserImageController::class, 'store'])->name('store');
            //remove avatar or wall
            Route::delete('', [UserImageController::class, 'destroy'])->name('destroy');
        });
        //work with a specific profile
        Route::prefix('{user}')->group(function () {
            //work with follow feature
            Route::prefix('follow')->name('follow.')->group(function () {
                //follow another user
                Route::post('', [FollowController::class, 'store'])->name('store');
                //unfollow a user
                Route::delete('', [FollowController::class, 'destroy'])->name('destroy');
            });
            //see list of a user's followers
            Route::get('followers', [FollowerController::class, 'index'])->name('followers.index');
            //see list of a user's followings
            Route::get('followings', [FollowingController::class, 'index'])->name('followings.index');
        });
    });
});

