<?php

use App\Http\Controllers\Profile\FollowerController;
use App\Http\Controllers\User\FollowController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

//actions that need authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('users')->name('users.')->group(function () {
        //update the authenticated user's profile
        Route::patch('', [UserController::class, 'update'])->name('update');
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
        });
    });
});

