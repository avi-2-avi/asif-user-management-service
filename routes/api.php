<?php

use App\Http\Controllers\UserController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/profile', [UserController::class, 'profile'])->middleware('auth:api');

#Route::get('/test-auth', function () {
#    return response()->json(['user' => auth()->user()]);
#})->middleware('auth:api');
