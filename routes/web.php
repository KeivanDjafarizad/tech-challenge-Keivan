<?php

use App\Http\Controllers\JournalsController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth', 'prefix' => 'clients'], function () {
    Route::get('/', 'ClientsController@index')->name('clients.index');
    Route::get('/create', 'ClientsController@create');
    Route::post('/', 'ClientsController@store');
    Route::get('/{client}', 'ClientsController@show');
    Route::delete('/{client}', 'ClientsController@destroy');

    Route::post('/{client}/journals', [JournalsController::class, 'store']);
    Route::delete('/{client}/journals/{journal}', [JournalsController::class, 'destroy']);
});
