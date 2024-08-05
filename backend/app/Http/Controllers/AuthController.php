<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
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
