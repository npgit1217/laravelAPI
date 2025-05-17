<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup(Request $request){
        
        $validateUser = Validator::make(
            $request -> all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
            ]
        );

        if($validateUser->fails()){
                return response()->json([
                    'status'=>false,
                    'message'=>'Validation error',
                    'errors'=>$validateUser->errors()->all()
                ],401);
            }

       
        $user = User::create([
            'name'=>$request->name,
            'email'=> $request->email,
            'password' => $request->password
        ]);

         return response()->json([
                    'status'=>true,
                    'message'=>'user Created Successfully',
                    'user' => $user
                ],200);
    }

    public function login(Request $request){
         $validateUser = Validator::make(
            $request -> all(),
            [
                'email' => 'required|email',
                'password' => 'required',
            ]
        );

        if($validateUser->fails()){
                return response()->json([
                    'status'=>false,
                    'message'=>'aurthentication fal',
                    'errors'=>$validateUser->errors()->all()
                ],404);
            }

        if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])){
            $authUser = Auth::user();
            return response()->json([
                    'status'=>true,
                    'message'=>'user login Successfully',
                    'token' => $authUser->createToken("API Token")->plainTextToken,
                    'token_type' => 'bearer'
                ],200);
        }else{
            return response()->json([
                    'status'=>false,
                    'message'=>'Email & password does not matched',
                ],401);
        }
    }

    public function logout(Request $request){
        $user = $request->user();
        //print_r($user);die;
        $user->tokens()->delete();
         return response()->json([
                    'status'=>true,
                    'message'=>'User Logout Successfully',
                ],200);
        }
}
