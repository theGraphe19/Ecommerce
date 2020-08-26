<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
        $this->middleware('auth:admin')->except('loginadmapp', 'regadmapp', 'aboutusadmapp', 'aboutussaveadm');
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

    public function regadmapp(Request $request){

        $adm = Admin::first();

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
            'about_us' => $adm->about_us,
            'about_us_img' => $adm->about_us_img,
        ]);

        $admin->save();
        return response()->json(['status' => 'success', 'message' => 'You are registered'], 200);
    }

    public function loginadmapp(Request $request){

        $admin = Admin::where('email', $request->input('email'))->first();

        if(!$admin) {
            return response()->json(['status' => 'error', 'message' => 'Admin not found'], 401);
        }

        $pass = $request->input('password');
        if(password_verify ( $pass, $admin->password )){
            $admin->api_token = Str::random(60);
            $admin->save();
            return response()->json(['status' => 'success', 'message' => 'Logged in succesfully'], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Incorrect password'], 401);
    }

    public function aboutussaveadm(Request $request){
        $admins = Admin::all();

        $this->validate($request, [
            'about_us' => 'required',
            'about_us_img' => 'required|file',
        ]);

        if($request->hasFile('about_us_img')) {
            $file = $request->about_us_img;
            //Get file name with the extension
            $fileNameWithExt = $file->getClientOriginalName();
            // echo $fileNameWithExt;

            //Get just Filename
            $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

            //Get just Extension
            $extension = $file->getClientOriginalExtension();

            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;

            //Upload Image
            Storage::disk('local')->putFileAs('about_us', $file, $fileNameToStore);
        }
        foreach($admins as $admin){
            $admin->about_us = $request->input('about_us');
            $admin->about_us_img = $fileNameToStore;
            $admin->save();
        }
        return response()->json(['status' => 'success', 'message' => 'Saved succesfully'], 200);
        // return response()->json($fileNameToStore);
    }

    public function aboutusadmapp(){
        $admin = Admin::first();
        $img = "http://developers.thegraphe.com/ecommerce/storage/app/about_us/" . $admin->about_us_img;
        return response()->json(['about_us'=>$admin->about_us , 'about_us_img'=>$img]);
    }
}
