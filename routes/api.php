<?php

use Illuminate\Http\Request;
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

Route::resource('calls', 'CallsController');
Route::resource('operators', 'OperatorsController');

/**
 * Pick a call with low priority
 */
Route::get('operators/{operatorID}/pick', 'OperatorsController@pick');
