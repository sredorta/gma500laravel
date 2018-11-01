<?php

use Illuminate\Http\Request;
use App\Cors;
use App\Company;
use App\User;
//use App\authJWT;

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

//Routes that works on any case, but we pass isLogged, access...
Route::group(['middleware' => 'any'], function ($router) {
    Route::get('auth/user', 'ApiController@getAuthUser');   //Return user from token
    Route::post('users/indexes' , 'ProfileController@getProfileIndexesByRole');  //Return indexes of members
    Route::post('users/data' , 'ProfileController@getProfileById');  //Get profile from id
});    

//Only if we are not loggedIn
Route::group(['middleware' => 'unregistered'], function ($router) {
    Route::post('auth/login', 'ApiController@login');       //Login user from credentials
    Route::post('auth/signup', 'ApiController@register');   //Create user and token from signup form 
    Route::get('auth/emailvalidate', 'ApiController@emailValidate');   //Return user from token
    Route::post('auth/resetpassword', 'ApiController@resetPassword');   //Resets password
});

//We are registered as any access
Route::group(['middleware' => 'registered'], function ($router) {
    Route::post('auth/logout', 'ApiController@logout');      //Logout user from token invalidation
    Route::get('auth/removeaccount', 'ApiController@removeAccount');   //Removes the account given by the token
    //Notifications part
    Route::post('notifications/delete', 'NotificationController@delete');
    Route::post('notifications/markread', 'NotificationController@markAsRead');
    Route::get('notifications/getAll', 'NotificationController@getAll');
});

//registered as member
Route::group(['middleware' => 'member'], function ($router) {
    Route::post('users/find' , 'ProfileController@findProfileByName');  //Get profile from id
    Route::get('users/getmembers' , 'ProfileController@getAllMembers');  //Get profile from id

});

//Returns all data from all users including roles and accounts
Route::group(['middleware' => 'admin'], function ($router) {
    Route::get('admin/users' , 'ProfileController@adminGetUsers');  //Get all profiles
    Route::get('admin/roles' , 'RoleController@getRoles');          //Get all Roles
    Route::get('admin/groups' , 'GroupController@getGroups');       //Get all Groups
    Route::post('admin/roles/attach' , 'RoleController@attachProfile');          //Adds a role to a profile
    Route::post('admin/roles/detach' , 'RoleController@detachProfile');    //Removes a role to a profile
    Route::post('admin/roles/create' , 'RoleController@create');    //Creates a new role
    Route::post('admin/roles/delete' , 'RoleController@delete');    //Creates a new role

    Route::post('admin/groups/add' , 'GroupController@add');        //Adds a group to a profile
    Route::post('admin/groups/remove' , 'GroupController@remove');  //Removes a group to a profile    
    Route::post('admin/accounts/add' , 'UserController@add');       //Adds account to profile and sends email  
    Route::post('admin/accounts/remove' , 'UserController@remove'); //Removes an account of a profile and sends email  
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

//Route::get('users', 'UserController@index');
//Route::get('users/role/{role}', 'UserController@getUsersByRole'); //Roles are Membre,Bureau,Board