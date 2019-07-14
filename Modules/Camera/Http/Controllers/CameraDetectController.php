<?php

namespace Modules\Camera\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Camera\Http\Requests\CreateCameraRequest;

class CameraDetectController extends BaseController
{
    private $message;
    private $url_detect_system;
    public function __construct()
    {
        $this->message = 'Error when working with Detect system!';
        $this->url_detect_system = getenv('URL_SYSTEM_FACE_DETECT');
    }
    public function storeCameraToSystemDetect(CreateCameraRequest $request,$camera_code){
        try{
            $request['camera_code'] = $camera_code;
            $client = new \GuzzleHttp\Client([
                'headers' => ['Content-Type' => 'multipart/form-data']
            ]);
            $response = $client->post(
                $this->url_detect_system.'/register',
                [
                    'multipart' => [
                        [
                            'name' => 'name',
                            'contents' => $request['camera_code']
                        ],
                        [
                            'name' => 'url',
                            'contents' => $request['camera_url']
                        ]
                    ]
                ]
            );
            $body = json_decode($response->getBody(), true);
            if (!isset($body['result']) || !$body['result']) {
                if(isset($body['message'])) $this->message = $body['message'];
                return [ false , $this->message ];
                // Write Log Error $body
            }
            return [ true , null];
        }catch (\Exception $exception){
            // Write Log Error $exception->getMessage()
            return [ false , $this->message ];
        }
    }
}
