<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatingProfileRequest;
use App\Models\Post;
use App\Models\Worker;
use App\Models\WorkerReview;
use App\Services\UpdatingProfileService;
use Illuminate\Http\Request;

class WorkerProfileController extends Controller
{
    public function userProfile()
    {
//        return response()->json(auth()->guard('worker')->user());
        $workerId=auth()->guard('worker')->id();
        $worker=Worker::with('posts.reviews')->find($workerId)->makeHidden('status','verification_token','verified_at');
        $reviews=WorkerReview::whereIn('post_id',$worker->posts()->pluck('id'));
        $rate =round($reviews->sum('rate')/$reviews->count('rate'),1);
        return response()->json([
            'data'=>array_merge($worker->toArray(),['rate'=>$rate]),
//            'rate'=>$reviews->sum('rate')/$reviews->count('rate'),
        ]);
    }
    public function edit()
    {
        return response()->json([
            'worker'=>Worker::find(auth()->guard('worker')->id())->makeHidden('status','verification_token','verified_at'),
        ]);
    }
    public function update(UpdatingProfileRequest $request)
    {
        return (new UpdatingProfileService())->update($request);
    }
    public function delete()
    {
        Post::where('worker_id',auth()->guard('worker')->id())->delete();
        return response()->json([
            'message'=>'deleted'
        ]);
    }
}
