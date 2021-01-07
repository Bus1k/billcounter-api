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

Route::middleware('auth:sanctum')->get('/test', function (Request $request) {
    return response(['msg' => \Illuminate\Support\Facades\Auth::user()]);
});

/*
 * USERS
 */
Route::prefix('/user')->group( function () {

    //Create User
    Route::post('/register', [\App\Http\Controllers\Api\UserController::class, 'store']);

    Route::post('/login', [\App\Http\Controllers\Api\LoginController::class, 'login']);

    Route::middleware('auth:sanctum')->group( function () {
        Route::post('/logout', [\App\Http\Controllers\Api\LoginController::class, 'logout']);
        Route::get('/all', [\App\Http\Controllers\Api\UserController::class, 'index']);
        Route::post('/edit/{user}', [\App\Http\Controllers\Api\UserController::class, 'update']);
        Route::delete('/delete/{id}', [\App\Http\Controllers\Api\UserController::class, 'destroy']);
        Route::get('/{user}', [\App\Http\Controllers\Api\UserController::class, 'show']);
    });
});

/*
 * BILLS
 */
Route::prefix('/bill')->group( function () {

    Route::middleware('auth:sanctum')->group( function () {
        Route::post('', [\App\Http\Controllers\Api\BillController::class, 'store']);
        Route::get('/all', [\App\Http\Controllers\Api\BillController::class, 'getAll']);
        Route::post('/edit/{bill}', [\App\Http\Controllers\Api\BillController::class, 'update']);
        Route::delete('/delete/{bill}', [\App\Http\Controllers\Api\BillController::class, 'destroy']);
        Route::get('/{bill}', [\App\Http\Controllers\Api\BillController::class, 'show']);
    });

});
