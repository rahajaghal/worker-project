<?php

namespace App\Http\Controllers;

use App\Http\Requests\Client\ClientOrderRequest;
use App\Interfaces\CrudRepoInterfaceInterface;
use App\Models\ClientOrder;
use Illuminate\Http\Request;

class ClientOrderController extends Controller
{
    protected $crudRepo;
    public function __construct(CrudRepoInterfaceInterface $crudRepo)
    {
        $this->crudRepo=$crudRepo;
    }
    public function addOrder(ClientOrderRequest $request)
    {
//        $clientId=auth()->guard('client')->id();
//        if (ClientOrder::where('client_id',$clientId)->where('post_id',$request->post_id)->exists()){
//            return response()->json([
//                'message'=>'duplicate order request'
//            ],406);
//        }
//        $data= $request->all();
//        $data['client_id']=$clientId;
//        $order= ClientOrder::create($data);
//        return response()->json([
//            'message'=>'success'
//        ]);
         return $this->crudRepo->store($request);
    }
    public function workerOrder()
    {
        $orders= ClientOrder::with('post','client')->whereStatus('pending')->whereHas('post',function ($query){
            $query->where('worker_id',auth()->guard('worker')->id());
        })->get();
        return response()->json([
            'orders'=>$orders
        ]);
    }
    public function update($id ,Request $request)
    {
        $order =ClientOrder::findOrFail($id);
        $order->setAttribute('status',$request->status)->save();
//        $order->update(['status',$request->status]);
        return response()->json([
            'message'=>'updated'
        ]);
    }
}
