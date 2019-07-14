<?php

namespace Modules\Diary\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Diary\Entities\Diary;
use DB;
use Yajra\DataTables\DataTables;
class DiaryController extends BaseController
{

    public function  CreateDiary($camera_id,$person_id,$diary_content){
        $dataInput = [
            'camera_id'=> $camera_id,
            'person_id'=> $person_id,
            'diary_content' => $diary_content
        ];
        $diary = Diary::create($dataInput);
        return $this->responseSuccess($diary,'Created diary success!');
    }

    public function show($idDiary)
    {
        $diary = DB::table('diaries')
            ->join('persons','diaries.person_id','persons.person_id')
            ->join('cameras','diaries.camera_id','cameras.camera_id')
            ->join('person_images','person_images.person_id','persons.person_id')
            ->where('diaries.diary_id',$idDiary)
            ->select('diaries.diary_id','diaries.diary_content','diaries.created_at','persons.person_first_name','persons.person_code','cameras.camera_name','cameras.camera_code','person_images.image_url')
            ->orderBy('diary_id', 'DESC')
            ->first();
        if (is_null($diary)) return $this->responseBadRequest('Diary id not found!');
            $flat = json_decode($diary->diary_content);
            $diary->image_detect = $flat->image_detect;
            $diary->score = $flat->score;
            $diary->image_url =$this->HandlingUrl($diary->image_url);
            unset($diary->diary_content);
        return $this->responseSuccess($diary,'Show diary success!');
    }

    public function  showAll(){
        $data = DB::table('diaries')
            ->join('persons','diaries.person_id','persons.person_id')
            ->join('cameras','diaries.camera_id','cameras.camera_id')
            ->join('person_images','person_images.person_id','persons.person_id')
            ->select('diaries.diary_content','diaries.created_at','persons.person_first_name','persons.person_code','cameras.camera_name','cameras.camera_code','person_images.image_url')
            ->orderBy('diary_id', 'DESC')->paginate(9);
        foreach ($data as $key => $value){
                $flat = json_decode($value->diary_content);
                $data[$key]->image_detect = $flat->image_detect;
                $data[$key]->score = $flat->score;
                $data[$key]->image_url =$this->HandlingUrl($value->image_url);
                unset($data[$key]->diary_content);
        }
        return $this->responseSuccess($data,'Show all diary success!');
    }
    public function HandlingUrl($url)
    {
        return url('').'/'.$url;
    }

}
