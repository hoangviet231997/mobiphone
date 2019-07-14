<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'screen','namespace' => 'Modules\Screen\Http\Controllers','middleware'=>'CheckToken'], function(Router $api) {
        $api->post('/store', 'ScreenController@store');
        $api->get('/destroy/{screen_id}', 'ScreenController@destroy');
        $api->post('/update/{screen_id}', 'ScreenController@update');
        //show
        $api->get('/show/{screen_id}', 'ScreenController@show');
        //show all
        $api->get('/show-all', 'ScreenController@showAll');
        //on off screen
        $api->get('/on-of-screen/{screen_id}/{status}', 'ScreenController@onScreen');
    });
});

