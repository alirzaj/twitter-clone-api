<?php

use App\Http\Controllers\Feed\HomeController;
use App\Http\Controllers\Search\SearchController;
use Illuminate\Support\Facades\Route;

require 'authentication.php';
require 'user.php';
require 'tweet.php';

Route::middleware('auth:sanctum')->group(function () {
    //see the authenticated user's home (feed)
    Route::get('home', [HomeController::class, 'index'])->name('home.index');
    //work with search feature
    Route::prefix('search')->name('search.')->group(function () {
        //search sth (basic)
        Route::get('', [SearchController::class, 'show'])->name('show');
        //see tweets of a hashtag
        Route::get('tags', [SearchController::class, 'hashtag'])->name('tags.show');
    });
});
