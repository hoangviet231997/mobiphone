<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\User\Entities\User;
use Modules\User\Http\Requests\CreateUserRequest;
use Modules\User\Http\Requests\LoginRequest;
use Hash;
use JWTAuth;


class UserController extends BaseController
{
    public function store(CreateUserRequest $request){
        $dataInput = $request->dataOnly();
        $dataInput['user_pass'] = Hash::make($dataInput['user_pass']);
        $user =User::create($dataInput);
        return $this->responseSuccess($user,'Create User success !');
    }


    public function login(LoginRequest $request)
    {
        $dataInput = $request->dataOnly();
        $user = User::where('user_email',$dataInput['user_email'])->first();
        if (is_null($user)){
            return response()->json(['message' => 'Account information is incorrect !'], 401);
        }
        if (!Hash::check($dataInput['user_pass'],$user->user_pass)) {
            return response()->json(['message' => 'Account information is incorrect !'], 401);
        }
        $token = JWTAuth::fromUser($user);
        return $this->responseSuccess(['token' => $token],'Login success!');
    }
}
