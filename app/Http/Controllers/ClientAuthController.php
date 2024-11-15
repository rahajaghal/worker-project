<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClientAuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:client', ['except' => ['login','register']]);
    }

    public function register(Request $request){

        $validator=Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:workers',
            'password' => 'required|string|min:6',
            'photo' => 'required|image|mimes:jpg,png,jpeg',

        ]);
        if ($validator->fails()){
            return response()->json($validator->errors()->toJson(),400);
        }

        $image= $request->file('photo')->getClientOriginalName();

        $user=Client::create(array_merge(
            $validator->validated(), [
                'password'=>bcrypt($request->password),
                'photo'=>$request->file('photo')->storeAs('clients',$image)
            ]
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
        if (!$token=auth()->guard('client')->attempt($validator->validated())){
            return response()->json(['error'=>'unauthorized'],401);
        }
        return $this->createNewToken($token);
    }

    public function logout()
    {
        auth()->guard('client')->logout();
        return response()->json([
            'message' => 'user Successfully logged out',
        ]);
    }


    public function refresh()
    {
        return $this->createNewToken(auth()->guard('client')->refresh());
    }
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>auth()->factory()->getTTL() *60 ,
            'user'=>auth()->guard('client')->user()
        ]);
    }
    public function userProfile()
    {
        return response()->json(auth()->guard('client')->user());
    }




}
