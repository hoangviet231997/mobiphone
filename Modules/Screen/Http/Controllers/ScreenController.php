<?php

namespace Modules\Screen\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\CameraScreen\Entities\CameraScreen;
use Modules\Screen\Entities\Screen;
use Modules\Screen\Http\Requests\CreateScreenRequest;
use Modules\Screen\Http\Requests\UpdateScreenRequest;
use DB;
use Yajra\DataTables\DataTables;

class ScreenController extends BaseController
{
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(CreateScreenRequest $request)
    {

        $dataInput = $request->dataOnly();
        if(!$request->screen_code){
            $dataInput['screen_code'] = Helper::generateRandomString(8);
        }
        $screen = Screen::create($dataInput);
        return $this->responseSuccess($screen,'Create screen success!');

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($screen_id)
    {
        $screen = collect(DB::select('call sp_show_detail_screen(?)',array($screen_id)))->first();
        if(!$screen){
            return $this->responseBadRequest('Screen not found!');
        }
        return $this->responseSuccess($screen,'Show detail success!');
    }
    public function showAll()
    {
        $screen = Screen::get();
        return DataTables::of($screen)->make(true);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateScreenRequest $request, $screen_id)
    {
        $screen = Screen::find($screen_id);
        $dataInput = $request->dataOnly();
        if(!$screen){
            return $this->responseBadRequest('Screen not found!');
        }
        $screen->update($dataInput);
        return $this->responseSuccess($screen,'Update screen success');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($screen_id)
    {
        return 1;
        $screen = Screen::find($screen_id);
        $camera_screen = CameraScreen::where('screen_id',$screen_id)->first();
        if($camera_screen){
            return $this->responseBadRequest('Screen exist in table Camera_screen!');
        }
        if(!$screen){
            return $this->responseBadRequest('Screen not found!');
        }
        $screen->delete();
        return $this->responseSuccess(null,'Delete screen success!');
    }

    public function onScreen($screen_id,$status){
        $screen = Screen::find($screen_id);
        if(!$screen){
            return $this->responseBadRequest('Screen not found!');
        }
        $screen->screen_status = $status;
        $screen->update();
        return $this->responseSuccess($screen,'status screen success!');

    }


}
