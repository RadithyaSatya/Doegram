<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Session\Middleware\StartSession;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Authentication
Route::post('/auth/login',[AuthController::class,'login']);

Route::middleware([StartSession::class])->group(function (){
    //Verification Email
    Route::post('/verify/send',[AuthController::class,'sendVerificationEmail']);
    Route::get('/verify/email/{token}',[AuthController::class,'verifyEmail'])->name('verification.mail');

    //Register
    Route::post('/auth/register',[AuthController::class,'register']);
});

