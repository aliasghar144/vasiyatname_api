<?php


/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/


$router->group(['prefix' => 'login', 'middleware' => 'throttle'], function () use ($router) {
    $router->post('/auth/check_mobile', 'AuthController@checkMobile');
    $router->post('/auth/verify_otp', 'AuthController@verifyOtp');
});


$router->group(['prefix' => 'financial', 'middleware' => 'sanctum', 'namespace' => 'Financial'], function () use ($router) {
    $router->group(['prefix' => 'debts'], function () use ($router) {
        $router->get('/', 'DebtController@index');
        $router->get('/details/{id}', 'DebtController@detailsindex');
        $router->post('/', 'DebtController@store');
        $router->put('/{id}', 'DebtController@update');
        $router->delete('/{id}', 'DebtController@destroy');
    });
    $router->group(['prefix' => 'claim'], function () use ($router) {
        $router->get('/', 'ClaimController@index');
        $router->get('/details/{id}', 'ClaimController@detailsindex');
        $router->post('/', 'ClaimController@store');
        $router->put('/{id}', 'ClaimController@update');
        $router->delete('/{id}', 'ClaimController@destroy');
    });
});

$router->group(['prefix' => 'religious', 'middleware' => 'sanctum', 'namespace' => 'Religious'], function () use ($router) {
    
    $router->group(['prefix' => 'prayers'], function () use ($router) {
        $router->get('/', 'PrayersController@index');
        $router->put('/', 'PrayersController@update');
    });

    $router->group(['prefix' => 'fasting'], function () use ($router) {
        $router->get('/', 'FastingController@index');
        $router->put('/', 'FastingController@update');
    });

    //    $router->group(['prefix'=>'claim'],function()use($router){
    //        $router->get('/', 'ClaimController@index');
    //        $router->post('/', 'ClaimController@store');
    //        $router->put('/{id}', 'ClaimController@update');
    //        $router->delete('/{id}', 'ClaimController@destroy');
    //    });
});


$router->group([
    'prefix' => 'user',
    'middleware' => 'sanctum',
], function () use ($router) {
    $router->post('/complete_profile', 'ProfileController@completeProfile');
    $router->get('/', 'ProfileController@getInformation');
    $router->post('/logout', action: "ProfileController@logout");
});
