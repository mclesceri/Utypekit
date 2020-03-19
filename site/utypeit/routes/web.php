<?php
*/
//Auth::routes();
	Route::get('/orderslist', 'OrdersController@orderslist');
Route::get('/user/verify/{token}', 'Auth\RegisterController@verifyUser');
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
// Registration Routes...
Route::get('/register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('/register', 'Auth\RegisterController@register');
// Password Reset Routes...