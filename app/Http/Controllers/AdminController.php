<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['login','register']]);
    }

    public function register(Request $request){

        $validator=Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors()->toJson(),400);
        }
        $user=Admin::create(array_merge(
            $validator->validated(),
            ['password'=>bcrypt($request->password)]
        ));
        return response()->json([
            'message'=>'user successfully registered',
            'user'=>$user
        ],201);
    }

    public function login(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        if (!$token=auth()->attempt($validator->validated())){
            return response()->json(['error'=>'unauthorized'],401);
        }
        return $this->createNewToken($token);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return response()->json([
            'message' => 'user Successfully logged out',
        ]);
    }


    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>auth()->factory()->getTTL() *60 ,
            'user'=>auth()->user()
        ]);
    }
    public function userProfile()
    {
        return response()->json(auth()->user());
    }


}
