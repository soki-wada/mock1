<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserProfileController extends Controller
{
    //
    public function storeProfile(ProfileRequest $request){
        $user = Auth::user();
        $is_profile = $user->profile;
        $profile = $request->only([
            'username',
            'postal_code',
            'address',
            'building',
        ]);
        $profile['user_id'] = Auth::id();
        
        if($request->hasFile('image')){
            if($is_profile && $is_profile->image){
                Storage::delete('public/images/' . $is_profile->image);
            }

            $fileName = $request->file('image')->getClientOriginalName();
            $uniqueName = Str::uuid() . '_' . $fileName;
            $request->file('image')->storeAs('public/images', $uniqueName);
            $profile['image'] = basename($uniqueName);
        }

        if($is_profile){
            $is_profile->update($profile);
            return redirect('/mypage');
        }else{
            Profile::create($profile);
            return redirect('/');
        }
    }

    public function showMypage(){
        $tab = request()->query('tab', 'sell');
        $user = Auth::user();
        $profile = $user->profile;
        $soldProducts = $user->products;
        $purchasedProducts = $user->purchases;
        return view('mypage', compact('profile', 'soldProducts', 'purchasedProducts', 'tab'));
    }
}
