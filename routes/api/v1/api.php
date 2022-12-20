<?php


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
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::prefix('/user')->group(function(){
    Route::post('/login', 'App\Http\Controllers\LoginController@login');
    Route::post('/createUser', 'App\Http\Controllers\LoginController@createUser');
    Route::post('/logout', 'App\Http\Controllers\LoginController@logout');
    //con este middleware protejemos las rutas dentro de /user
    Route::middleware('auth:api')->get('/all', 'App\Http\Controllers\LoginController@all');
});

Route::prefix('/files')->group(function(){
    //con este middleware protejemos las rutas dentro de /user
    Route::middleware('auth:api')->get('/all', 'App\Http\Controllers\LoginController@all');
});
