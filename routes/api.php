<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('boards','boardsController@index');
Route::post('saveItem/{board}/{data}','boardsController@saveItem');
Route::post('deleteItem/{board}/{item}','boardsController@deleteItem');
Route::post('addBoard/{board}','boardsController@addBoard');
Route::post('deleteBoard/{board}','boardsController@deleteBoard');
