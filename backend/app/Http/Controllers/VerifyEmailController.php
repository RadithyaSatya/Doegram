<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class VerifyEmailController extends Controller
{
    public function sendVerificationEmail(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|string|email|max:255'
        ]);
        if($validator->fails()){
            return response()->json(['status'=>false,'message'=>$validator->errors()],422);
        }
        $checkUserEmail = User::where('email',$request->email)->first();
        if($checkUserEmail){
            return response()->json([
                'status'=>false,
                'messages'=>'This email is already registered'
            ],401);
        }
        $token = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $request->session()->put('email_verification_token',$token);
        $request->session()->put('email',$request->email);
        $request->session()->put('email_verification_token_exp_at',now()->addMinutes(1));
        Mail::to($request->email)->send(new VerifyEmail($token));
        return response()->json(['status'=>true,'message'=>'Verification email sent. Please check your email.'],200);
    }

    public function verifyEmail($token, Request $request){
        if($request->session()->get('email_verified') == true){
            return response()->json(['status'=>true, 'message'=>'Email verified, please Register your account']);
        }
        if($request->session()->get('email')==""){
            return response()->json([
                'status'=>false,
                'message'=>'Please Send Email First'
            ],422);
        }
        if($token === $request->session()->get('email_verification_token')){
            $request->session()->put('email_verified',true);
            return response()->json(['status'=>true,'message'=>'Verification email successfully.']);
        }
        return response()->json(['status'=>true,'message'=>'Verification token is invalid'],401);
    }
    public function cekExpToken(){

    }
}
