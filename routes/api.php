<?php

use Illuminate\Http\Request;
use App\Company;
use App\User;
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

Route::group(['middleware' => ['api','cors']], function () {
    Route::post('auth/login', 'ApiController@login');
    Route::group(['middleware' => 'jwt.auth'], function () {
        Route::get('user', 'ApiController@getAuthUser');
    });
});




//Product Cathegories
Route::get('config/product/cathegories', 'ConfigProductCathegoryController@index');
Route::get('config/product/cathegories/{id}', 'ConfigProductCathegoryController@show');
Route::post('config/product/cathegories', 'ConfigProductCathegoryController@store');
Route::put('config/product/cathegories/{id}', 'ConfigProductCathegoryController@update');
Route::delete('config/product/cathegories/{id}', 'ConfigProductCathegoryController@delete');

//Product Types
Route::get('config/product/types', 'ConfigProductTypeController@index');
Route::get('config/product/types/{id}', 'ConfigProductTypeController@show');
Route::post('config/product/types', 'ConfigProductTypeController@store');
Route::put('config/product/types/{id}', 'ConfigProductTypeController@update');
Route::delete('config/product/types/{id}', 'ConfigProductTypeController@delete');




Route::get('products', 'ProductController@index');
Route::get('products/{id}', 'ProductController@show');
//Route::post('products', 'ProductController@store');
//Route::put('products/{id}', 'ProductController@update');
//Route::delete('products/{id}', 'ProductController@delete');

Route::get('users', 'UserController@index');
Route::get('users/role/{role}', 'UserController@getUsersByRole'); //Roles are Membre,Bureau,Board