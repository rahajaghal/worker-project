<?php

namespace App\Http\Controllers;

use App\Filters\PostFilter;
use App\Http\Requests\StoringPostRequest;
use App\Models\Post;
use App\Services\PostService\StoringPostService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PostController extends Controller
{
    public function store(StoringPostRequest $request)
    {

//        try {
//            DB::beginTransaction();
//            $data= $request->except('photos');
//            $data['worker_id']=auth()->guard('worker')->id();

//        $data['status']="pending";

//            $post= Post::create($data);
//            if ($request->hasfile('photos')){
//                foreach ($request->file('photos') as $photo){
//                    $image= $photo->getClientOriginalName();
//                    $postPhotos = new PostPhoto();
//                    $postPhotos->post_id=$post->id;
//                    $postPhotos->photo= $photo->storeAs('posts',$image);
//                    $postPhotos->save();
//                }
//            }
//            DB::commit();
//            return response()->json([
//               "message"=> "post has been created successfully",
//            ]);
//        }catch (Exception $e){
//            DB::rollBack();
//            return $e->getMessage();
//        }
        return (new StoringPostService())->store($request);
    }
    public function index()
    {
        $posts=Post::all();
        return response()->json([
            'posts'=>$posts
        ]);
    }
    public function approved()
    {
//        $posts=Post::with('worker:id,name')->where('status','approved')->get()->makeHidden('status');
        $posts =QueryBuilder::for(Post::class)
            ->allowedFilters((new PostFilter())->filter())
            ->with('worker:id,name')
            ->where('status','approved')
            ->get(['id','content','price','worker_id']);
        return response()->json([
            'posts'=>$posts
        ]);
    }
}
