<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerifyEmailController;
use Illuminate\Session\Middleware\StartSession;
use App\Http\Middleware\ExpiredTokenVerifyEmail;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Authentication
Route::post('/auth/login',[AuthController::class,'login']);

Route::middleware([StartSession::class,ExpiredTokenVerifyEmail::class])->group(function (){
    //Verification Email
    Route::post('/verify/send',[VerifyEmailController::class,'sendVerificationEmail']);
    Route::get('/verify/email/{token}',[VerifyEmailController::class,'verifyEmail'])->name('verification.mail');

    //Register
    Route::post('/auth/register',[AuthController::class,'register']);
});

