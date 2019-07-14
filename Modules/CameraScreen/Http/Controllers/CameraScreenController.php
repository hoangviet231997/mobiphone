<?php

namespace Modules\CameraScreen\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\CameraScreen\Entities\CameraScreen;
use Modules\CameraScreen\Http\Requests\CreateCameraScreenRequest;
use Modules\CameraScreen\Http\Requests\UpdateCameraScreenRequest;
use DB;
use Yajra\DataTables\DataTables;

class CameraScreenController extends BaseController
{
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(CreateCameraScreenRequest $request)
    {

        $dataInput = $request->dataOnly();
        foreach ($dataInput['screen_id'] as $key => $value){
            $dataInput['screen_id'] = $value;
            $group = CameraScreen::where('camera_id','=',$dataInput['camera_id'])->where('screen_id','=',$dataInput['screen_id'])->count();
            if($group){
                return $this->responseBadRequest('Camera and screen already exists! ');
            }
            $group = CameraScreen::create($dataInput);
            $data[] = $group;
        }
        return $this->responseSuccess($data,'Create group success!');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($group_id)
    {
        $group = DB::table('camera_screen')
            ->leftJoin('cameras','camera_screen.camera_id','=','cameras.camera_id')
            ->join('screens','camera_screen.screen_id','=','screens.screen_id')
            ->select('camera_screen.camera_screen_id','cameras.camera_name','screens.screen_name','cameras.camera_id','screens.screen_id')
            ->where('camera_screen_id',$group_id)
            ->first();
        if(!$group){
            return $this->responseBadRequest('Group not found!');
        }
        return $this->responseSuccess($group,'Show detail success!');
    }

    public function showAll()
    {
        $group = DB::table('camera_screen')
            ->leftJoin('cameras','camera_screen.camera_id','=','cameras.camera_id')
            ->join('screens','camera_screen.screen_id','=','screens.screen_id')
            ->select('camera_screen.*','cameras.camera_name','screens.screen_name','camera_screen.camera_id')
            ->orderby('camera_screen.camera_id','DESC')
            ->get();
        return DataTables::of($group)->make(true);
    }
    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateCameraScreenRequest $request, $group_id)
    {
        $group = CameraScreen::find($group_id);
        $dataInput = $request->dataOnly();
        if(!$group){
            return $this->responseBadRequest('Group not found!');
        }
        $group = CameraScreen::where('camera_screen_id',$group_id)->where('camera_id','=',$request->camera_id)->where('screen_id','=',$request->screen_id)->first();
        if($group){
            return $this->responseBadRequest('Camera and screen already exists! ');
        }
        $group = CameraScreen::find($group_id);
        $group->update($dataInput);
        return $this->responseSuccess($group,'Show group detail success!');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($group_id)
    {
        $group = CameraScreen::find($group_id);
        if(!$group){
            return $this->responseBadRequest('Group not found!');
        }
        $group->delete();
        return $this->responseSuccess(null,'delete group success!');
    }
}
