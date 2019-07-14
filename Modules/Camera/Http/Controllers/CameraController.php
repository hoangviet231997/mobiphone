<?php

namespace Modules\Camera\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Camera\Entities\Camera;
use Modules\Camera\Http\Requests\CreateCameraRequest;
use Modules\Camera\Http\Requests\UpdateCameraRequest;
use Modules\CameraScreen\Entities\CameraScreen;
use Yajra\DataTables\DataTables;

class CameraController extends BaseController
{

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(CreateCameraRequest $request)
    {
        $camera_code = '';
        $code = Helper::generateRandomString(36);
        $traverse = function ($code, &$camera_code) use (&$traverse) {
            $camera = Camera::where('camera_code',$code)->first();
            $camera_code = $code;
            if(!$camera){
                return;
            }
            $code = Helper::generateRandomString(36);
            $traverse($code, $camera_code);
        };
        $traverse($code, $camera_code);

        $dataInput = $request->dataOnly();
        if(!$request->camera_code){
            $dataInput['camera_code'] = $camera_code;
        }
        $camera = Camera::create($dataInput);
        return $this->responseSuccess($camera,'Create camera success!');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($camera_id)
    {
        $camera = Camera::find($camera_id);
        if(!$camera){
            return $this->responseBadRequest('Camera not found!');
        }
        return $this->responseSuccess($camera,'Create camera success!');
    }

    public function showAll()
    {
        $camera =Camera::get();
        return DataTables::of($camera)->make(true);
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateCameraRequest $request, $camera_id)
    {
        $camera = Camera::find($camera_id);
        $dataInput = $request->dataOnly();
        if(!$camera){
            return $this->responseBadRequest('Camera not found!');
        }
        $camera->update($dataInput);
        return $this->responseSuccess($camera,'Update Camera success!');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($camera_id)
    {
        $camera = Camera::find($camera_id);
        $camera_screen = CameraScreen::where('screen_id',$camera_id)->first();
        if($camera_screen){
            return $this->responseBadRequest('Camera exist in table Camera_screen!');
        }
        if(!$camera){
            return $this->responseBadRequest('Camera not found!');
        }
        $camera->delete();
        return $this->responseSuccess(null,'Delete Camera success!');
    }

    public function onCamera($camera_id,$status){
        $camera = Camera::find($camera_id);
        if(!$camera){
            return $this->responseBadRequest('Camera not found!');
        }
        $camera->camera_status = $status;
        $camera->update();
        return $this->responseSuccess($camera,'Status Camera success!');

    }
}
