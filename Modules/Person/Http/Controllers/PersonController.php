<?php

namespace Modules\Person\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Requests\BaseRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Person\Entities\Person;
use DB;
use Modules\Person\Entities\PersonImage;
use Modules\Diary\Entities\Diary;
use Modules\Camera\Entities\Camera;
use Modules\Detect\Http\Controllers\DetectController;
use GuzzleHttp\Client;
use Modules\Person\Http\Requests\CallBackRequest;
use Modules\Person\Http\Requests\StoreFaceRequest;
use Yajra\DataTables\DataTables;
use Illuminate\Filesystem\Filesystem;

class PersonController extends BaseController
{

    public function store(StoreFaceRequest $request)
    {

        DB::beginTransaction();
        try {
            $dataInput = $request->dataOnly()();
            $dataInput['person_code'] = $this->randCode();
            unset($dataInput['image_url']);
            $person = Person::create($dataInput);
            $listPersonImage = null;
            if ($request->hasFile('image_url')) {
                foreach ($request->image_url as $image) {
                    $person_image = $this->uploadFile($image, 'Person');
                    $dataPersonImage = [
                        'person_id' => $person->person_id,
                        'image_url' =>$person_image
                    ];
                    $personImage = PersonImage::create($dataPersonImage);
                    $result =  $this->SendRequestAddPerson($image,$person->person_code);
                    if ($result)  $personImage->update(['image_status',1]);
                }
            }
            $this->trainingToSystemDetect();
            DB::commit();
            return $this->responseSuccess($person,'Created person success!');
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }

    }

    public function  SendRequestAddPerson($file,$person_code){
        $client = new Client();
        $res = $client->request('POST', getenv('URL_SYSTEM_FACE_DETECT').'add',[
            'multipart' => [
                [
                    'name' => 'group',
                    'contents' => $person_code
                ],
                [
                    'name' => 'file[]',
                    'contents' => fopen($file->getPathname(), 'r'),
                    'filename' => $file->getClientOriginalName()
                ]
            ]
        ]);
        $body = json_decode($res->getBody(), true);
        if (isset($body['result']) && $body['result']) return true;
        return false;
    }

    public function  SendRequestAddPersonExcel($file,$person_code){
        $client = new Client();
        $res = $client->request('POST', getenv('URL_SYSTEM_FACE_DETECT').'add',[
            'multipart' => [
                [
                    'name' => 'group',
                    'contents' => $person_code
                ],
                [
                    'name' => 'file[]',
                    'contents' => fopen($file, 'r'),
                    'filename' => $file
                ]
            ]
        ]);
        $body = json_decode($res->getBody(), true);
        if (isset($body['result']) && $body['result']) return true;
        return false;
    }

      public function trainingToSystemDetect(){
        try{
            $client = new Client();
            $res = $client->request('POST', getenv('URL_SYSTEM_FACE_DETECT').'train',[
                'multipart' => []
            ]);
            $body = json_decode($res->getBody(), true);
            if (isset($body['result']) && $body['result']) return true;
            return false;
        }catch (\Exception $exception){
            return $this->responseBadRequest('Error when working with Detect system! !');
        }
    }


    public function show($idPerson)
    {
        $person = Person::where('person_id',$idPerson)->first();
        if (is_null($person)) return $this->responseBadRequest('Information not found!');
        $personImage = PersonImage::where('person_id',$idPerson)->where('image_status',0)->get();
        $list_images = [];
        foreach ($personImage as $value){
            array_push($list_images,$this->HandlingUrl($value->image_url));
        }
        $person['list_images'] =$list_images;
        return $this->responseSuccess($person,'Show person success!');
    }

    public  function  showAll(){
        $data =DB::table('persons')->orderBy('person_id','DESC')->get();
        return DataTables::of($data)->make(true);
    }

    public function  GetListImagesByIdPerson($idPerson){
        $data_images = PersonImage::where('person_id',$idPerson)->where('image_status',1)->select('image_id','image_url')->get();
        if (count($data_images) == 0) return $this->responseBadRequest('Information not found!');
        foreach ($data_images as $key =>  $value){
            $data_images[$key]->image_url =$this->HandlingUrl($value->image_url);
        }
        return $this->responseSuccess($data_images,'Show list images success!');

    }


    // System Detect callback
    public function  callBackFromSystemDetect(CallBackRequest  $request){
        $dataInput = $request->dataOnly();
        $checkPerson = Person::where('person_code',$dataInput['person_code'])->first();
        if (is_null($checkPerson)) return $this->responseBadRequest('Person code not found.');
        $checkCamera = Camera::where('camera_code',$dataInput['camera_code'])->first();
        if (is_null($checkCamera)) return $this->responseBadRequest('Camera code not found.');
        $this->WriteDiary($checkCamera->camera_id,$checkPerson->person_id,json_encode($dataInput));
        if ($dataInput['score'] >= getenv('MIN_SCORE_DETECT')){
            $dataInput['image_source'] = $this->GetUrlImageByPersonId($checkPerson->person_id);
            $detectController = new DetectController();
            $data = $detectController->sendMessages($checkPerson,$dataInput);
            return $this->responseSuccess($data,'Successful! Logged data for processing!');
        }
        return $this->responseSuccess(null,'Minimum score through 80');
    }




    public  function  GetUrlImageByPersonId($person_id){
        $person_image = PersonImage::where('person_id',$person_id)->first();
        return getenv('APP_URL').$person_image->image_url;
    }


     public function uploadFile($requestFile, $nameFolder)
    {
        $fileExtension = trim($requestFile->getClientOriginalName());
        $date = date('d-m-y');
        $fileName = $date . "_" . time().rand('100', '999'). " _" . preg_replace('/[, ]+/', '_', $fileExtension);
        $requestFile->storeAs('public/' . $nameFolder . '/' . $date . '/', $fileName);
        return '/storage/app/public/' . $nameFolder . '/' . $date . '/' . $fileName;
    }
    public function HandlingUrl($url)
    {
        return url('').'/'.$url;
    }

        public function randCode() {
        $length = 7;
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $size = strlen( $chars );
        $str = '';
        for( $i = 0; $i < $length; $i++ ) {
            $str .= $chars[ rand( 0, $size - 1 ) ];
        }
        return $str;
    }
    public  function WriteDiary($camera_id,$person_id,$diary_content){
        $dataInput = [
            'camera_id'=> $camera_id,
            'person_id'=> $person_id,
            'diary_content' => $diary_content
        ];
        $diary = Diary::create($dataInput);
        return $this->responseSuccess($diary,'Created diary success!');
    }


    //Import file excel


    public function uploadFileImport($requestFile, $nameFolder)
    {
        $fileExtension = trim($requestFile->getClientOriginalName());
        $date = date('d_m_y');
        $fileName = $date.rand('100', '999'). "_" . preg_replace('/[, ]+/', '_', $fileExtension);
        $requestFile->storeAs('public/'. $nameFolder ,$fileName);
        return [
            '/storage/app/public/' . $nameFolder . '/',
            $fileName
        ];
    }


    public function  InportExcel(Request $request){

        if ($request->hasFile('file_url')) {
//            $file = new Filesystem;
//            $file->cleanDirectory('storage/app/public/import_excel');
//            $zipper = new \Chumper\Zipper\Zipper;
//            list($dir_folder, $file_name) = $this->uploadFileImport($request->file_url, 'import_excel');
//            $zipper->make($dir_folder.$file_name)->extractTo($dir_folder);
//            $zipper->close();
            $dir_folder = "/storage/app/public/import_excel/";

            //
            $excel = \Excel::load($dir_folder.'data.xlsx', function($resader) {})->get();
            if(!empty($excel) && $excel->count()) {
                $db=$excel->toArray();
                $no_image = [];
                foreach ($db as $item => $row) {
                    if (!empty($row)) {
                        $dataInput = [];
                        $storage_path_image = "";
                        foreach ($row as $key => $value) {
                            $storage_path_image = '/public/import_excel/images/'.$row['id'].'.jpg';
                            $path_image = './storage/app'.$storage_path_image;
                            if ($this->CheckExistsFileImage($path_image)){
                                $person_gender = 0;
                                if($row['gioi_tinh'] == 'Nam')  $person_gender = 1;
                                $dataInput = [
                                    'person_code' => $row['id'],
                                    'person_first_name'=>$row['ho'] ,
                                    'person_last_name'=> $row['ten'],
                                    'person_gender'=> $person_gender,
                                    'person_birthday'=> $row['ngay_sinh'],
                                    'person_content'=> json_encode($row),
                                ];
                            }

                        }
                        if(!empty($dataInput)){
                            $this->storePersonByFileExcel($dataInput,$storage_path_image);
                        }
                        else{
                            $no_image[] = $row['id'];
                        }
                    }
                }
                return $this->responseSuccess($no_image,"Them du lieu thanh cong");
            }

        }else{
            return $this->responseBadRequest('File not exists!');
        }

    }

    public function  storePersonByFileExcel($dataInput,$image){
        DB::beginTransaction();
//        try {
            $person = Person::create($dataInput);

            $path_upload = 'public/Person/'.date('d-m-y');
            if(!\Storage::exists($path_upload)) {
                \Storage::makeDirectory($path_upload,0777);
            }
            $image_name_new = $path_upload.'/'.$dataInput["person_code"].'_'.time().'_'.rand('100', '999').'.jpg';
            \Storage::copy($image, $image_name_new);
            $dataPersonImage = [
                'person_id' => $person->person_id,
                'image_url' => '/storage/app/'.$image_name_new
            ];
            $personImage = PersonImage::create($dataPersonImage);
            $result =  $this->SendRequestAddPersonExcel('./storage/app/'.$image_name_new,$person->person_code);
            if ($result)  $personImage->update(['image_status',1]);
            $this->trainingToSystemDetect();

            DB::commit();
//        } catch (\Exception $e) {
//            DB::rollback();
//            // Ghi log loi
//            return $e->getMessage();
//        }
    }

    public function  CheckExistsFileImage($path){
        return file_exists($path);
    }
}
