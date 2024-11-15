<?php

namespace App\Services\WorkerService;

use App\Mail\VerificationEmail;
use App\Models\Worker;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WorkerRegisterService
{
    protected $model;
    public function __construct()
    {
        $this->model=new Worker();
    }
    function validation($request)
    {
        $validator=  Validator::make($request->all(),$request->rules());
        if ($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        return $validator;
    }
    public function store($data,$request)
    {
        $image= $request->file('photo')->getClientOriginalName();
        $worker=$this->model->create(array_merge(
            $data->validated(), [
                'password'=>bcrypt($request->password),
                'photo'=>$request->file('photo')->storeAs('workers',$image),
            ]
        ));
        return $worker->email;
    }
    public function generateToken($email)
    {
        $token = substr(md5(rand(0,9).$email.time()),0,32);
        $worker=$this->model->whereEmail($email)->first();
        $worker->verification_token=$token;
        $worker->save();
        return $worker;
    }
    public function sendEmail($worker)
    {
        Mail::to($worker->email)->send(new VerificationEmail($worker));
    }
    public function register($request)
    {
        try {
            DB::beginTransaction();
            $data=$this->validation($request);
            $email= $this->store($data,$request);
            $worker=$this->generateToken($email);
            $this->sendEmail($worker);
            DB::commit();
            return response()->json([
                "message"=>"account has been created,please check your email"
            ]);
        }catch (Exception $e){
            DB::rollBack();
            return $e->getMessage();

        }

    }
}
