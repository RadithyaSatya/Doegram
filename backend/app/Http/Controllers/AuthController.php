<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\VerifyEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
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
                'messages'=>'email ini sudah terdaftar'
            ],401);
        }
        $token = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $request->session()->put('email_verification_token',$token);
        $request->session()->put('email',$request->email);
        Mail::to($request->email)->send(new VerifyEmail($token));
        return response()->json([
            'status'=>true,
            'message'=>'Verification email sent. Please check your email.'
        ],200);
    }

    public function verifyEmail($token, Request $request){
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

    public function register(Request $request){
        if(!$request->session()->get('email_verified')){
            return response()->json(['status'=>false,'message'=>'Please Verify your email first'],400);
        }
        $validator = Validator::make($request->all(),[
            'name'=>'required|string|max:255',
            'username'=>'required|string|max:55',
            'password'=>'required',
        ]);
        if($validator->fails()){
            return response()->json(['status'=>false,'message'=>$validator->errors()],422);
        }
        $user = User::create([
            'name'=>$request->name,
            'username'=>$request->username,
            'password'=>Hash::make($request->password),
            'email'=>$request->session()->get('email'),
            'email_verified_at'=>now()
        ]);
        $request->session()->forget(['email_verification_token','email','email_verified']);
        return response()->json(['status'=>true,'message'=>'Register Successfully','data'=>$user]);
    }

    public function login(Request $request){
        try{
            $user = User::where("username",$request->identity)
            ->orWhere("email",$request->identity)
            ->first();
            if($user && Hash::check($request->password, $user->password)){
                $token = $user->createToken("auth_sanctum")->plainTextToken;
                return response()->json([
                    "status"=>true,
                    "token"=>$token,
                    "message"=>"Login Success",
                    "data"=>$user
                ],200);
            }else{
                return response()->json([
                    "status"=>false,
                    "message"=>"Your Account is Wrong Please Try Again"
                ],401);
            }
        }catch(\Throwable $e){
            return response()->json([
                "status"=>false,
                "message"=>$e->getMessage()
            ],500);
        }
    }
}
