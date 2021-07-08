<?php

use App\Http\Controllers\Profile\FollowController;
use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

//actions that need authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('users')->name('users.')->group(function () {
        //update the authenticated user's profile
        Route::patch('', [ProfileController::class, 'update'])->name('update');
        //work with a specific profile
        Route::prefix('{user}')->group(function () {
            //work with follow feature
            Route::prefix('follow')->name('follow.')->group(function () {
                //follow another user
                Route::post('', [FollowController::class, 'store'])->name('store');
            });

        });
    });
});

