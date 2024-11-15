<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDashboard\AdminNotificationController;
use App\Http\Controllers\AdminDashboard\PostStatusController;
use App\Http\Controllers\ClientAuthController;
use App\Http\Controllers\ClientOrderController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\WorkerAuthController;
use App\Http\Controllers\WorkerProfileController;
use App\Http\Controllers\WorkerReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//Route::controller(AdminController::class)

//    ->middleware('DbBackup')
//    ->prefix('auth/admin')
//    ->group(function (){
//    Route::post('register','register');
//    Route::post('login', 'login');
//    Route::post('refresh', 'refresh');
//    Route::post('logout','logout');
//});
//
//
//Route::controller(WorkerAuthController::class)
//    ->middleware('DbBackup')
//    ->prefix('auth/worker')
//    ->group(function (){
//        Route::post('register','register');
//        Route::post('login', 'login');
//        Route::post('refresh', 'refresh');
//        Route::post('logout','logout');
//    });
//
//Route::controller(ClientAuthController::class)
//    ->middleware('DbBackup')
//    ->prefix('auth/client')
//    ->group(function (){
//        Route::post('register','register');
//        Route::post('login', 'login');
//        Route::post('refresh', 'refresh');
//        Route::post('logout','logout');
//    });
Route::middleware('DbBackup')->prefix('auth')->group(function (){

    Route::controller(AdminController::class)
        ->prefix('admin')
        ->group(function (){
            Route::post('register','register');
            Route::post('login', 'login');
            Route::post('refresh', 'refresh');
            Route::post('logout','logout');
            Route::get('user_profile','userProfile');
        });
    Route::controller(WorkerAuthController::class)
        ->prefix('worker')
        ->group(function (){
            Route::post('register','register');
            Route::post('login', 'login');
            Route::post('refresh', 'refresh');
            Route::post('logout','logout');
            Route::get('verify/{token}','verify');
        });
    Route::controller(ClientAuthController::class)
        ->prefix('client')
        ->group(function (){
            Route::post('register','register');
            Route::post('login', 'login');
            Route::post('refresh', 'refresh');
            Route::post('logout','logout');
            Route::get('user_profile','userProfile');
        });

});
Route::controller(PostController::class)
    ->prefix('worker/post')->group(function (){

       Route::post('add','store')->middleware('auth:worker');
       Route::get('show','index')->middleware('auth:admin');
       Route::get('approved','approved');
    });
Route::prefix('worker')->group(function (){
    Route::get('pending/orders',[ClientOrderController::class,'workerOrder'])->middleware('auth:worker');
    Route::put('update/order/{id}',[ClientOrderController::class,'update'])->middleware('auth:worker');
    Route::post('review',[WorkerReviewController::class,'store'])->middleware('auth:client');
    Route::get('review/post/{postId}',[WorkerReviewController::class,'postRate']);
    Route::get('profile',[WorkerProfileController::class,'userProfile']);
    Route::get('profile/edit',[WorkerProfileController::class,'edit']);
    Route::post('profile/update',[WorkerProfileController::class,'update'])->middleware('auth:worker');
    Route::get('profile/posts/delete',[WorkerProfileController::class,'delete'])->middleware('auth:worker');
});

Route::controller(PostStatusController::class)
    ->prefix('admin/post')->group(function (){

        Route::post('status','changeStatus');
    });

Route::controller(AdminNotificationController::class)
    ->middleware('auth:admin')
    ->prefix('admin/notifications')
    ->group(function (){
    Route::get('/all','index');
    Route::get('/unread','unread');
    Route::get('/markReadAll','markReadAll');
    Route::delete('/deleteAll','deleteAll');
        Route::delete('/delete/{id}','delete');
});
Route::prefix('client')->group(function (){
    Route::controller(ClientOrderController::class)->prefix('/order')->group(function (){
        Route::post('request','addOrder')->middleware('auth:client');
    });
});
