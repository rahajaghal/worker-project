<?php

namespace App\Services\PostService;

use App\Models\Admin;
use App\Models\Post;
use App\Models\PostPhoto;
use App\Notifications\AdminPost;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;


class StoringPostService
{
    protected $model;
    public function __construct()
    {
        $this->model=new Post();
    }
    public function adminPercent($price)
    {
        $discount=$price * 0.05;
        $priceAfterDiscount=$price- $discount;
        return $priceAfterDiscount;
    }
    public function storePost($data)
    {
        $data= $data->except('photos');
        $data['worker_id']=auth()->guard('worker')->id();
        $data['price']= $this->adminPercent( $data['price']);
//        $data['status']="pending";
        $post= Post::create($data);
        return $post;
    }
    public function storePostPhotos($request,$postId)
    {
        foreach ($request->file('photos') as $photo){
            $image= $photo->getClientOriginalName();
            $postPhotos = new PostPhoto();
            $postPhotos->post_id=$postId;
            $postPhotos->photo= $photo->storeAs('posts',$image);
            $postPhotos->save();
        }
    }
    public function sendAdminNotification($post)
    {
        $admins=Admin::get();
        Notification::send($admins, new AdminPost(auth()->guard('worker')->user(),$post));
    }
    public function store($request)
    {
        try {
            DB::beginTransaction();
            $post=$this->storePost($request);
            if ($request->hasFile('photos')){
                $postPhotos= $this->storePostPhotos($request,$post->id);
            }
            $this->sendAdminNotification($post);
            DB::commit();
            return response()->json([
                "message"=> "post has been created successfully, your price after discount is {$post->price}",
            ]);
        }catch (Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }

    }
}
