<?php

namespace App\Http\Controllers\api\v1\user;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\User;
use Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|regex:/^[a-zA-z0-9]+$/'
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = User::where('email', $request->input('email'))->first();
        if(!$user){
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        $pass = $request->input('password');
        if(password_verify ( $pass, $user->password )){
            $user->api_token = Str::random(60);
            $user->save();
            return response()->json(['status' => 'success', 'message' => 'Logged in succesfully'], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Incorrect password'], 401);
    }

    public function reg(Request $request)
    {
        // $adm = User::first();

        $rules = [
            'name' => 'required|regex:/^[A-Za-z ]+$/',
            'phone' => 'required|min:8|max:10|unique:users|regex:/^[0-9]+$/',
            'email' => 'required|email|unique:users',
            'password' => 'required|regex:/^[^\W_]+$/|min:8|max:13',
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $name = $request->input('name');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $pass = $request->input('password');
        $password = (new BcryptHasher)->make($request->input('password'));

        $user = new User([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'password' => $password,
            'status' => 1,
            'api_token' => Str::random(60),
        ]);

        $user->save();
        return response()->json(['status' => 'success', 'message' => 'You are registered as user!'], 200);
    }
}
