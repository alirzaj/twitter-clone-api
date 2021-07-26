<?php

use App\Http\Controllers\Feed\HomeController;
use Illuminate\Support\Facades\Route;

require 'authentication.php';
require 'user.php';
require 'tweet.php';

Route::middleware('auth:sanctum')->group(function () {
    //see the authenticated user's home (feed)
    Route::get('home', [HomeController::class, 'index'])->name('home.index');
});
