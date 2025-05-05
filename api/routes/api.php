<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/jobs'], function () {
    Route::get('/', [App\Http\Controllers\JobPostController::class, 'index']);
    Route::get('/{jobPost}', [App\Http\Controllers\JobPostController::class, 'show']);
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
