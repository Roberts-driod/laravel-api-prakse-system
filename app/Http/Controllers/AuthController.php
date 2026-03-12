<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller 
{


    public function register(Request $request){

        $fields = $request->validate([
            "name" => "required|max:255",
            "email" => "required|unique:users|email",
            "password" => "required|confirmed",
        ]);

        $user = User::create($fields);

        $token = $user->createToken($request->name);

        return [
            "user" => $user,
            "token" => $token->plainTextToken
        ];
    }

    public function login(Request $request){

             $request->validate([
            "email" => "required|exists:users|email",
            "password" => "required",
        ]);

        $user = User::where("email" , $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            return ["Message"=> "The provided credentials is incorrect"];
        }

        $token = $user->createToken($user->name);

        return [
            "user" => $user,
            "token" => $token->plainTextToken
        ];

    }

    public function logout(Request $request){

        $request->user()->tokens()->delete();

        return [
            "message" => "youre logged out "
        ];
    }

}
