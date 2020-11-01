<?php

namespace App\Http\Controllers\api\v1\user;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;
use App\User;
use Auth;

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
            return response()->json(['status' => 'error', 'message' => 'User not found', 'data' => null], 404);
        }

        $pass = $request->input('password');
        if(password_verify ( $pass, $user->password )){
            $user->api_token = Str::random(60);
            $user->save();
            return response()->json(['status' => 'success', 'message' => 'Logged in succesfully', 'data' => (object)['user' => $user, 'token' => $user->api_token]], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Incorrect password', 'data' => (object)['user' => $user, 'token' => $user->api_token]], 401);
    }

    public function reg(Request $request)
    {
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
        ]);

        $user->save();
        return response()->json(['status' => 'success', 'message' => 'You are registered as user!', 'data' => (object)['user' => $user, 'token' => $user->api_token]], 200);
    }

    public function logout()
    {
        $user = Auth::user();
        $user->api_token = null;
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'You are logged out!', 'data' => null], 200);
    }
}
