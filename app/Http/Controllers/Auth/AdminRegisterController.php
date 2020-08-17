<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Admin;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Hashing\BcryptHasher;

class AdminRegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth/admin-register');
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|regex:/^[A-Za-z ]+$/',
            'phone' => 'required|min:8|max:10|regex:/^[0-9]+$/',
            'email' => 'required|email|unique:admins',
            'password' => 'required|regex:/^[^\W_]+$/|min:8|max:13',
        ]);

        $admin = Admin::create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'password' => (new BcryptHasher)->make($request->input('password')),
            'status' => 1,
        ]);

        if(Auth::guard('admin')->attempt( ['email' => $request->email, 'password' => $request->password], $request->remember)){
            return redirect()->intended(route('admin.dashboard'));
        }
        return redirect()->back()->withInput($request->only('email', 'remember'));
    }
}
