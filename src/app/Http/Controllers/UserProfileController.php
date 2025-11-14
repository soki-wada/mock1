<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Deal;
use App\Models\Evaluation;
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
        $tab = request()->query('tab', 'sell', 'deal');
        $user = Auth::user();
        $profile = $user->profile;
        $soldProducts = $user->products;
        $dealingProducts = Deal::where(function ($query) use ($user) {
            $query->where('selling_user_id', $user->id)
                ->orWhere('purchasing_user_id', $user->id);
        })
            // ★ 追加：評価が存在しない取引だけを取得
            ->whereDoesntHave('evaluations', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['unreadMessages' => function ($query) use ($user) {
                $query->where('user_id', '!=', $user->id)
                    ->where('is_read', false);
            }])
            ->withCount(['unreadMessages as unread_messages_count' => function ($query) use ($user) {
                $query->where('user_id', '!=', $user->id)
                    ->where('is_read', false);
            }])
            ->get()
            ->sortByDesc(function ($deal) {
                $latest = $deal->unreadMessages->max('created_at');
                return $latest ?? now()->subYears(100);
            });

        $purchasedProducts = $user->purchases;
        $evaluations = Evaluation::where('target_user_id', $user->id)->avg('rating');
        $averageRating = round($evaluations, 0);
        return view('mypage', compact('profile', 'soldProducts', 'dealingProducts', 'purchasedProducts', 'averageRating', 'tab'));
    }
}
