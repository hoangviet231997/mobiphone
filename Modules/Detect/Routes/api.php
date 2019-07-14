<?php

use Dingo\Api\Routing\Router;

$api = app(Router::class);
$api->version('v1', function (Router $api) {
    $api->group([
        'prefix' => 'detect',
        'namespace' => 'Modules\Detect\Http\Controllers'
    ], function (Router $api) {

        $api->get('/messages', 'DetectController@getMessages');

        $api->get('/send-messages', 'DetectController@sendMessages');

    });
});
