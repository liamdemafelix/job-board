<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/jobs', [App\Http\Controllers\JobPostController::class, 'index']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
