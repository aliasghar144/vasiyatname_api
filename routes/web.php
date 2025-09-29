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

$router->group([
    'prefix' => 'user',
    'middleware' => 'sanctum',
], function () use ($router) {
    $router->post('/complete_profile', 'ProfileController@completeProfile');
});
