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


Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('municipalities', 'ProjectController@getMunicipalities');
Route::post('barangays', 'ProjectController@generateBarangays');
Route::post('data', 'ProjectController@generateData');
Route::post('chart', 'ProjectController@generateChart');

