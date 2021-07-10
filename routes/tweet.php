<?php

use App\Http\Controllers\Tweet\TweetController;
use Illuminate\Support\Facades\Route;

//work with tweets
Route::prefix('tweets')->middleware('auth:sanctum')->name('tweets.')->group(function () {
    //add a tweet
    Route::post('', [TweetController::class, 'store'])->name('store');
});
