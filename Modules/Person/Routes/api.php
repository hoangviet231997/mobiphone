<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'person','namespace' => 'Modules\Person\Http\Controllers'], function(Router $api) {
        $api->post('/store', 'PersonController@store');
        $api->get('/show-all', 'PersonController@showAll')->middleware('CheckToken');
        $api->get('/show/{idPerson}', 'PersonController@show')->middleware('CheckToken');
        $api->get('/get-list-images/{idPerson}', 'PersonController@GetListImagesByIdPerson')->middleware('CheckToken');
        $api->post('/import-excel', 'PersonController@InportExcel');
    });
    $api->group(['prefix' => 'detect','namespace' => 'Modules\Person\Http\Controllers'], function(Router $api) {
        $api->post('/alert', 'PersonController@callBackFromSystemDetect');
    });

});
