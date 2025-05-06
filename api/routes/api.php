<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/keywords', [App\Http\Controllers\JobPostController::class, 'keywords']);
Route::group(['prefix' => '/jobs'], function () {
    Route::get('/', [App\Http\Controllers\JobPostController::class, 'index']);
    Route::post('/', [App\Http\Controllers\JobPostController::class, 'store']);
    Route::get('/posts', [App\Http\Controllers\JobPostController::class, 'posts']);
    Route::get('/{jobPost}', [App\Http\Controllers\JobPostController::class, 'show']);
    Route::post('/{jobPost}/moderate', [App\Http\Controllers\JobPostController::class, 'moderate']);
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
