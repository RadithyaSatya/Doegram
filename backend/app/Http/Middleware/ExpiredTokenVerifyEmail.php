<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExpiredTokenVerifyEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if($request->session()->get('email_verification_token_exp_at') != "" && $request->session()->get('email_verification_token_exp_at') < now()){
            $request->session()->forget(['email_verification_token','email','email_verified','email_verification_token_exp_at']);
            return response()->json(['status'=>false,'message'=>'Token Expired'],200);
        }
        return $next($request);
    }
}
