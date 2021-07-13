<?php

use App\Http\Controllers\Tweet\ReplyController;
use App\Http\Controllers\Tweet\TweetController;
use Illuminate\Support\Facades\Route;

//work with tweets
Route::prefix('tweets')->middleware('auth:sanctum')->name('tweets.')->group(function () {
    //add a tweet
    Route::post('', [TweetController::class, 'store'])->name('store');
    //work with a specific tweet
    Route::prefix('{tweet}')->group(function () {
        //see a tweet
        Route::get('', [TweetController::class, 'show'])->name('show');
        //work with retweets
        Route::prefix('replies')->name('replies.')->group(function () {
            //retweet a tweet
            Route::post('', [ReplyController::class, 'store'])->name('store');
        });
    });
});
