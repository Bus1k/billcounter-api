<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
 * USERS
 */
Route::prefix('/user')->group( function () {

    //Create User
    Route::post('/create', [\App\Http\Controllers\Api\UserController::class, 'store']);

    //Returns Bearer token
    Route::post('/login', [\App\Http\Controllers\Api\LoginController::class, 'login']);


    Route::middleware('auth:api')->group( function () {
        Route::get('/all', [\App\Http\Controllers\Api\UserController::class, 'index']);
        Route::get('/{user}', [\App\Http\Controllers\Api\UserController::class, 'show']);
        Route::post('/edit/{user}', [\App\Http\Controllers\Api\UserController::class, 'update']);
        Route::post('/delete/{id}', [\App\Http\Controllers\Api\UserController::class, 'destroy']);
    });

});
