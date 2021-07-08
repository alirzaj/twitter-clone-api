<?php

use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

//actions that need authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('users')->name('users.')->group(function () {
        //update the authenticated user's profile
        Route::patch('', [ProfileController::class, 'update'])->name('update');
    });


});

