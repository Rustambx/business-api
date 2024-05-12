<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::group([
    'middleware' => 'api',
], function () {

    Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::post('me', [\App\Http\Controllers\Api\AuthController::class, 'me']);

    Route::get('notes', [\App\Http\Controllers\Api\NoteController::class, 'index']);
    Route::post('notes', [\App\Http\Controllers\Api\NoteController::class, 'store']);
    Route::get('notes/{id}', [\App\Http\Controllers\Api\NoteController::class, 'show']);
    Route::post('notes/update/{id}', [\App\Http\Controllers\Api\NoteController::class, 'update']);
    Route::delete('notes/delete/{id}', [\App\Http\Controllers\Api\NoteController::class, 'delete']);

});
