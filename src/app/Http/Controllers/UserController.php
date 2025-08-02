<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class UserController extends Controller
{
    //
    public function emailAuth(){
        return view('auth.email_auth');
    }

    public function login(LoginRequest $request){
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ])->withInput($request->only('email'));
    }

    public function storeUser(RegisterRequest $request){
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        Auth::login($user);
        event(new Registered($user));
        return redirect()->route('verification.notice');
    }

    public function verify(EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/mypage/profile');
    }

    public function resend(Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back();
    }

    public function showProfile(){
        $user = Auth::user();
            $profile = $user->profile;
            return view('profile', compact('profile'));
    }
}
