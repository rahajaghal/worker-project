<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\WorkerStoreRequest;
use App\Models\Worker;
use App\Services\WorkerService\WorkerLoginService\WorkerLoginService;
use App\Services\WorkerService\WorkerRegisterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class WorkerAuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:worker', ['except' => ['login','register','verify']]);
    }

    public function register(WorkerStoreRequest $request){

//        $validator=Validator::make($request->all(),[
//            'name' => 'required|string|max:255',
//            'email' => 'required|string|email|max:255|unique:workers',
//            'password' => 'required|string|min:6',
//            'phone' => 'required|string|max:17',
//            'photo' => 'required|image|mimes:jpg,png,jpeg',
//            'location' => 'required|string|min:6',
//        ]);
//        if ($validator->fails()){
//            return response()->json($validator->errors()->toJson(),400);
//        }
//
//        $image= $request->file('photo')->getClientOriginalName();
//
//        $user=Worker::create(array_merge(
//            $validator->validated(),
//            ['password'=>bcrypt($request->password),
//             'photo'=>$request->file('photo')->storeAs('workers',$image)
//            ]
//        ));
//        return response()->json([
//            'message'=>'user successfully registered',
//            'user'=>$user
//        ],201);
        return (new WorkerRegisterService)->register($request);
    }
    public function verify($token)
    {
        $worker=Worker::whereVerificationToken($token)->first();
        if (!$worker){
            return response()->json([
                "message"=>"this token is invalid"
            ]);
        }
        $worker->verification_token=null;
        $worker->verified_at=now();
        $worker->save();
        return response()->json([
            "message"=>"your account has been verified"
        ]);
    }

    public function login(LoginRequest $request)
    {
//        $validator=Validator::make($request->all(),[
//            'email' => 'required|string|email',
//            'password' => 'required|string',
//        ]);
//        if ($validator->fails()){
//            return response()->json($validator->errors(),422);
//        }
//        if (!$token=auth()->guard('worker')->attempt($validator->validated())){
//            return response()->json(['error'=>'unauthorized'],401);
//        }
//        return $this->createNewToken($token);
        return (new WorkerLoginService())->login($request);
    }

    public function logout()
    {
        auth()->guard('worker')->logout();
        return response()->json([
            'message' => 'user Successfully logged out',
        ]);
    }


    public function refresh()
    {
        return $this->createNewToken(auth()->guard('worker')->refresh());
    }
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>auth()->factory()->getTTL() *60 ,
            'user'=>auth()->guard('worker')->user()
        ]);
    }

}
