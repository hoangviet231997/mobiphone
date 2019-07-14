<?php

use Dingo\Api\Routing\Router;

$api = app(Router::class);
$api->version('v1', function (Router $api) {
    $api->group([
        'prefix' => 'weather',
        'namespace' => 'Modules\Weather\Http\Controllers'
    ], function (Router $api) {

        $api->get('/forecast', 'WeatherController@getWeather');

    });
});