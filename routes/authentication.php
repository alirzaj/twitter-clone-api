<?php

use App\Http\Controllers\Authentication\RegisterController;
use Illuminate\Support\Facades\Route;

//actions that only guest users can take
Route::middleware('guest')->group(function () {
    //register a user
    Route::post('register', [RegisterController::class, 'store'])->name('register');
});
