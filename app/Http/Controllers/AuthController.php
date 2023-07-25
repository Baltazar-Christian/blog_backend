<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //Register user
    public function register(Request $request)
    {
        // Validate parameters 
        $attrs=$request->validate(
            [
                'name'=>'required|string',
                'email'=>'required|email|unique:users,email',
                'password'=>'required|min:6|confirmed'
            ]
        );

        // Create a new user
        $user= User::create(
            [
                'name' => $attrs['name'],
                'email' => $attrs['email'],
                'password' =>bcrypt($attrs['password'])
            ]
        );

        // return user and token in response
        return response([
            'user'=>$user,
            'token'=>$user->createToken('secret')->plainTextToken
        ],200
            
        );
    }

    // For login user 
    public function login(Request $request)
    {
        // Validate parameters 
        $attrs=$request->validate(
            [
                'email'=>'required|email',
                'password'=>'required|min:6'
            ]
        );

        //Attempt login
        if(!Auth::attempt($attrs))
        {
            return response(
                [
                    'message' =>'Invalid credentials.'
                ],403
            );
        }

        // return user and token in response
        $user=auth()->user();
        return response([
            'user'=>$user,
            'token'=> $user->createToken('secret')->plainTextToken
        ]
            
        );
    }

    // logout user
    public function logout() {

        auth()->user()->tokens()->delete();
        return response(
            [
                'message' =>'Logged out successfully'
            ],200
        );
    }


    // get user details 
    public function user()
    {
        return response(
            [
                'user' => auth()->user()
            ],200
        );
    }

    // update user details
    public function update(Request $request)
    {
        $attrs=$request->validate(
            [
                'name' => 'required|string'
            ]
        );

        $image=$this->saveImage($request->image,'profiles');

        auth()->user()->update(
            [
                'name' => $attrs['name'],
                'image' => $image
            ]
        );
        return response(
            [
                'message'=>'profile updated',
                'user' => auth()->user()
            ],200
        );
    }

}
