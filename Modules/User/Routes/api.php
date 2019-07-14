<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'user','namespace' => 'Modules\User\Http\Controllers'], function(Router $api) {
        $api->post('/create', 'UserController@store');
        $api->post('/login', 'UserController@login');
    });

});
