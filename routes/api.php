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

//API route for login user
Route::post('/getlogin', [App\Http\Controllers\API\AuthController::class, 'getLogin']);
Route::post('/setlogin', [App\Http\Controllers\API\AuthController::class, 'setLogin']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
