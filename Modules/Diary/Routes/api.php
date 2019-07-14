<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'diary','namespace' => 'Modules\Diary\Http\Controllers','middleware'=>'CheckToken'], function(Router $api) {
        $api->get('/show-all', 'DiaryController@showAll');
        $api->get('/show/{idDiary}', 'DiaryController@show');
    });

});
