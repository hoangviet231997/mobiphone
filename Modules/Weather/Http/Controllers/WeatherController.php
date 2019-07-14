<?php

namespace Modules\Weather\Http\Controllers;

use App\Http\Controllers\BaseController;

class WeatherController extends BaseController
{
    public function getWeather(){
        $data = ['weather'=>"Clouds", 'temp'=>"21", 'icon'=>"04d"];
        $client_open = new \GuzzleHttp\Client([
            'headers' => [ 'Content-Type' => 'application/json']
        ]);
        $response_open = $client_open->get(
            getenv('WEATHER_URL').'/data/2.5/weather?id='.getenv('WEATHER_CITY_ID').'&APPID='.getenv('WEATHER_APP_ID').'&units='.getenv('WEATHER_UNITS').'&lang'.getenv('MF_LANGUAGE').''
        );
        $body_open = json_decode($response_open->getBody(), true);
        if(isset($body_open['weather'][0]['description']) && isset($body_open['weather'][0]['icon']) && isset($body_open['main']['temp']) ) {
            $data = ['weather' => ucwords($body_open['weather'][0]['description']), 'temp' => $body_open['main']['temp'], 'icon' => $body_open['weather'][0]['icon']];
        }
        return response()->json($data);
    }
}
