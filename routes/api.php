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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

/*
 * USERS
 */
Route::prefix('/user')->group( function () {

    //Returns Bearer token
    Route::post('/login', [\App\Http\Controllers\Api\LoginController::class, 'login']);

    Route::middleware('auth:api')->group( function () {
        Route::get('/all', [\App\Http\Controllers\Api\UserController::class, 'index']);
    });

});
