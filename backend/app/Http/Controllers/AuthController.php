<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    function login(Request $request){
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
