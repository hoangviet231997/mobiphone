<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'camera','namespace' => 'Modules\Camera\Http\Controllers','middleware'=>'CheckToken'], function(Router $api) {
        $api->get('/destroy/{camera_id}', 'CameraController@destroy');
        $api->post('/update/{camera_id}', 'CameraController@update');
        //show
        $api->get('/show/{camera_id}', 'CameraController@show');
        //show all
        $api->get('/show-all', 'CameraController@showAll');
        //on off screen
        $api->get('/on-of-screen/{camera_id}/{status}', 'CameraController@onCamera');
    });
    $api->group(['prefix' => 'camera','namespace' => 'Modules\Camera\Http\Controllers'], function(Router $api) {
        $api->post('/store', 'CameraController@store');
    });
});

