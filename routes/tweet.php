<?php

use App\Http\Controllers\Tweet\ReplyController;
use App\Http\Controllers\Tweet\RetweetController;
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
        //work with replies
        Route::prefix('replies')->name('replies.')->group(function () {
            //reply a tweet
            Route::post('', [ReplyController::class, 'store'])->name('store');
        });
        //work with retweets
        Route::prefix('retweets')->name('retweets.')->group(function () {
            //retweet a tweet
            Route::post('',[RetweetController::class, 'store'])->name('store');
        });
    });
});
