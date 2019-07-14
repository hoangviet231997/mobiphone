<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'rule','namespace' => 'Modules\Rule\Http\Controllers','middleware'=>'CheckToken'], function(Router $api) {
        $api->post('/store', 'RuleController@store');
        $api->get('/show-all', 'RuleController@showAll');
        $api->get('/show/{idRule}', 'RuleController@show');
        $api->post('/update/{idRule}', 'RuleController@update');
        $api->get('/delete/{idRule}', 'RuleController@destroy');
        // on off rule
        $api->get('/on-off-rule/{idRule}/{status}', 'RuleController@onoffCamera');

    });

});
