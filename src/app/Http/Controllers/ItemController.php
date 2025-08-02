<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Condition;
use App\Models\Product;
use App\Models\Comment;
use App\Models\Favorite;
use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();
        $tab = request()->query('tab', false);
        $keyword = request()->query('keyword', '');

        if ($tab === 'mylist' && $user) {
            $products = $user->favorites()->with('product')->get()->pluck('product');

            if ($keyword) {
                $products = $products->filter(function ($product) use ($keyword) {
                    return mb_stripos($product->name, $keyword) !== false;
                });
            }
        } elseif($tab === 'mylist' && !$user){
            $products = collect();
        }else {
            $query = Product::query();

            if ($user) {
                $query->where('user_id', '!=', $user->id);
            }

            if ($keyword) {
                $query->keywordSearch($keyword);
            }

            $products = $query->get();
        }

        return view('index', compact('products', 'tab', 'keyword'));
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        $keyword = $request->keyword;
        $tab = $request->query('tab', false);

        if ($tab === 'mylist' && !$user) {
            return redirect()->route('login');
        }

        $query = Product::KeywordSearch($keyword);

        if ($tab === 'mylist' && $user) {
            $favoriteProductIds = $user->favorites()->pluck('product_id')->toArray();
            $query->whereIn('id', $favoriteProductIds);
        } else {
            if ($user) {
                $query->where('user_id', '!=', $user->id);
            }
        }

        $products = $query->get();
        return view('index', compact('products', 'keyword', 'tab'));
    }

    public function detail($item_id){
        $item = Product::with([
            'favorites', 'comments.user.profile', 'categories', 'condition'
            ])->find($item_id);

            $user = Auth::user();

            if($user){
                $hasFavorited = $user->favorites()->where('product_id', $item_id)->exists();
            }else{
                $hasFavorited = false;
            }

            return view('detail', compact('item', 'hasFavorited'));
    }

    public function comment(CommentRequest $request, $item_id){
        $comment = $request->only('content');
        $comment['user_id'] = Auth::id();
        $comment['product_id'] = $item_id;
        Comment::create($comment);
        return redirect("/item/$item_id");
    }

    public function toggle(Request $request)
    {
        $user = Auth::user();
        $productId = $request->input('item_id');

        $favorite = $user->favorites()->where('product_id', $productId)->first();

        if ($favorite) {
            $favorite->delete();
        } else {
            $user->favorites()->create([
                'user_id' => Auth::id(),
                'product_id' => $productId
            ]);
        }

        $count = Favorite::where('product_id', $productId)->count();

        return response()->json(['count' => $count]);
    }

    public function showPurchase($item_id){
        $product = Product::findOrFail($item_id);

        $user = Auth::user();
        $profile = $user->profile;

        $postal_code = session('purchase_postal_code') ?? $profile->postal_code;
        $address = session('purchase_address') ?? $profile->address;
        $building = session('purchase_building') ?? $profile->building;

        return view('purchase', compact('product', 'profile', 'postal_code', 'address', 'building'));
    }

    public function showAddress($item_id){
        $user = Auth::user();
        $profile = $user->profile;

        return view('address', compact('item_id', 'profile'));
    }

    public function updateAddress(AddressRequest $request, $item_id){
        session([
            'purchase_postal_code' => $request->postal_code,
            'purchase_address'     => $request->address,
            'purchase_building'    => $request->building,
        ]);

        return redirect("/purchase/{$item_id}");
    }

    public function showSell(){
        $categories = Category::all();
        $conditions = Condition::all();
        return view('sell', compact('categories', 'conditions'));
    }

    public function sell(ExhibitionRequest $request){
        $product = $request->only([
            'image',
            'condition_id',
            'name',
            'brand',
            'description',
            'price',
            'is_purchased'
        ]);
        $fileName = $request->file('image')->getClientOriginalName();
        $uniqueName = Str::uuid() . '_' . $fileName;
        $request->file('image')->storeAs('public/images', $uniqueName);
        $product['image'] = basename($uniqueName);

        $product['user_id'] = Auth::id();

        $product = Product::create($product);
        $product->categories()->attach($request->categories);

        return redirect('/');
    }
}
