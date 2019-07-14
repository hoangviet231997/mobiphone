<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'camera-screen','namespace' => 'Modules\CameraScreen\Http\Controllers','middleware'=>'CheckToken'], function(Router $api) {
        $api->post('/store', 'CameraScreenController@store');
        $api->get('/destroy/{group_id}', 'CameraScreenController@destroy');
        $api->post('/update/{group_id}', 'CameraScreenController@update');
        //show
        $api->get('/show/{group_id}', 'CameraScreenController@show');
        //show all
        $api->get('/show-all', 'CameraScreenController@showAll');
        //on off screen
        $api->get('/on-of-screen/{screen_id}', 'CameraScreenController@onScreen');
    });
});

