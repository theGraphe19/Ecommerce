<?php

namespace App\Http\Controllers\api\v1\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;
use App\Admin;
use Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|regex:/^[a-zA-Z0-9]+$/'
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $admin = Admin::where('email', $request->input('email'))->first();
        if(!$admin){
            return response()->json(['status' => 'error', 'message' => 'Admin not found', 'data' => null], 404);
        }

        $pass = $request->input('password');
        if(password_verify ( $pass, $admin->password )){
            $admin->api_token = Str::random(60);
            $admin->save();
            return response()->json(['status' => 'success', 'message' => 'Logged in succesfully', 'data' => (object)['admin' => $admin, 'token' => $admin->api_token]], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Incorrect password', 'data' => null], 401);
    }

    public function reg(Request $request)
    {
        $rules = [
            'name' => 'required|regex:/^[A-Za-z ]+$/',
            'phone' => 'required|min:8|max:10|unique:admins|regex:/^[0-9]+$/',
            'email' => 'required|email|unique:admins',
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

        $admin = new Admin([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'password' => $password,
            'status' => 1,
            'api_token' => Str::random(60),
        ]);

        $admin->save();
        return response()->json(['status' => 'success', 'message' => 'You are registered as admin!', 'data' => (object)['admin' => $admin, 'token' => $admin->api_token]], 200);
    }

    public function logout()
    {
        $admin = Auth::user();
        $admin->api_token = null;
        $admin->save();
        return response()->json(['status' => 'success', 'message' => 'Admin logged out!', 'data' => null], 200);
    }
}
