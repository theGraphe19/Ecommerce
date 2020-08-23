<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Admin;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin')->except(['regapp', 'loginapp', 'storeAbout', 'aboutusapp']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin');
    }

    public function regapp(Request $request){

        $this->validate($request, [
            'name' => 'required|regex:/^[A-Za-z ]+$/',
            'phone' => 'required|min:8|max:10|unique:admins|regex:/^[0-9]+$/',
            'email' => 'required|email|unique:admins',
            'password' => 'required|regex:/^[^\W_]+$/|min:8|max:13',
        ]);

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
        return response()->json(['status' => 'success', 'message' => 'You are registered'], 200);
    }

    public function loginapp(Request $request){

        $admin = Admin::where('email', $request->input('email'))->first();

        if(!$admin) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 401);
        }

        $pass = $request->input('password');
        if(password_verify ( $pass, $admin->password )){
            $admin->api_token = Str::random(60);
            $admin->save();
            return response()->json(['status' => 'success', 'message' => 'Logged in succesfully'], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Incorrect password'], 401);
    }

    public function storeAbout(Request $request){
        $admins = Admin::all();

        foreach($admins as $admin){
            $admin->about_us = $request->input('about_us');
            $admin->about_us_img = $request->input('about_us_img');
            $admin->save();
        }
        return response()->json(['status' => 'success', 'message' => 'Saved succesfully'], 200);
    }

    public function aboutusapp(){
        $admin = Admin::first();
        return response()->json($admin);
    }
}
