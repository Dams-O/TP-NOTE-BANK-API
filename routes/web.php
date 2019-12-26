<?php

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// /registerUserAccount
$router->post('registerUserAccount', 'AuthController@register');

// /login
$router->get('login', 'AuthController@login');

// Ici se trouveront les routes qui ne donne accès aux
// ressources que si l'on est identifié
$router->group(['prefix' => 'api'], function () use ($router) {

    // /logout attention cette route n'est pas vérifié par le middleware
    $router->get('logout', 'AuthController@logout');

    // /api/seeAccount
    $router->get('seeAccount', 'UserController@profile');

    // /api/addOrWithdrawMoney
    $router->get('addOrWithdrawMoney', 'BankOpsController@addOrWithdrawMoney');
    
    // /api/intraTransfert
    $router->get('intraTransfert', 'BankOpsController@intraTransfert');

    // /api/externalTransfert
    $router->get('externalTransfert', 'BankOpsController@externalTransfert');
});