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

/*Route::get('/', function () {
    return view('login');
});*/

Route::get('/buttons', 'MyController@ShowB')->name('button.index');
Route::get('/cards', 'MyController@ShowC')->name('card.index');
Route::get('login','LoginController@getLogin')->name('login');
Route::post('login','LoginController@postLogin');
Route::get('','MyController@getIndex')->middleware('auth');;
Route::get('register','MyController@getRegister');
Route::post('register','MyController@postRegister');
Route::get('register/verify/{code}', 'LoginController@verify');
Route::post('authenticate',  'LoginController@authenticate');
Route::get('forgot','MyController@getForgot');
Route::post('forgot','MyController@postForgot')->name('forgot');