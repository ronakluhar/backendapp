<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('auth.login');
//});

Route::get('/', 'HomeController@checkLogin');

Auth::routes();

Route::get('/home', 'UsersController@getUser')->name('home');

Route::get('/admin/add-user', 'UsersController@addUser');
Route::post('/admin/saveUser', 'UsersController@saveUser');
Route::get('/admin/users', 'UsersController@getUser');
Route::get('/admin/edituser/{id}', 'UsersController@editUser');
Route::get('/admin/deleteuser/{id}', 'UsersController@deleteUser');

Route::get('/admin/add-media', 'MediaController@addMedia');
Route::post('/admin/saveMedia', 'MediaController@saveMedia');
Route::get('/admin/media', 'MediaController@getMedia');
Route::get('/admin/deleteMedia/{id}', 'MediaController@deleteMedia');
Route::get('/admin/editMedia/{id}', 'MediaController@editMedia');


