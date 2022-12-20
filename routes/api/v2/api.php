<?php

use App\Http\Controllers\ArchivosController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PassportAuthController;


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

Route::prefix('/user')->group(function(){
    Route::post('/login', 'App\Http\Controllers\LoginController@login');
    Route::post('/createUser', 'App\Http\Controllers\LoginController@createUser');
    Route::post('/logout', 'App\Http\Controllers\LoginController@logout');
});


Route::get('index', [ArchivosController::class, 'index']);
Route::post('store', [ArchivosController::class, 'store']);
Route::get('files/{id}', [ArchivosController::class, 'show']);
Route::post('update', [ArchivosController::class, 'update']);
Route::post('delete', [ArchivosController::class, 'delete']);

// agregamos middleware a Files para que no se pueda acceder sin estar auth
Route::middleware('auth:api')->group(function () {
    Route::resource('files', ArchivosController::class);

});
