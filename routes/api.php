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
// USER
Route::name('me')->get('users/me', 'User\UserController@me');
Route::resource('users', 'User\UserController', ['only' => ['store', 'update']]);
Route::name('verify')->get('users/verify/{token}', 'User\UserController@verify');
Route::name('resend')->get('users/{user}/resend', 'User\UserController@resend');
Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
Route::group([    
    'namespace' => 'Auth',    
    'middleware' => 'api',    
    'prefix' => 'password'
], function () {    
    Route::post('create', 'PasswordResetController@create');
    Route::get('find/{token}', 'PasswordResetController@find');
    Route::post('reset', 'PasswordResetController@reset');
});

// REGULAR USER
Route::resource('regular_users', 'RegularUser\RegularUserController', ['only' => ['show']]);

Route::resource('regular_users.clients', 'RegularUser\ClientController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
Route::resource('regular_users.clients.loans', 'RegularUser\LoanController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
Route::resource('regular_users.clients.loans.payments', 'RegularUser\PaymentController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

Route::resource('regular_users.loans', 'RegularUser\LoansController', ['only' => ['index']]);
Route::resource('regular_users.payments', 'RegularUser\PaymentsController', ['only' => ['index']]);
