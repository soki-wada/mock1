<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PurchaseController extends Controller
{
    //
    public function checkout(PurchaseRequest $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $product = Product::findOrFail($request->product_id);

        session([
            'purchase_postal_code' => $request->postal_code,
            'purchase_address'     => $request->address,
            'purchase_building'    => $request->building,
        ]);


        $paymentType = $request->payment;

        if ($paymentType == '0') {
            //コンビニ払い：即時保存
            $fakeSessionId = uniqid('test_session_');

            return redirect()->route('purchase.success', ['session_id' => $fakeSessionId]);
        }

        $session =  \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card', 'konbini'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => $product->price,
                    'product_data' => [
                        'name' => $product->name,
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('purchase.form', ['item_id' => $product->id]),
            'metadata' => [
                'user_id' => Auth::id(),
                'product_id' => $product->id,
            ]
        ]);

        return redirect($session->url);
    }

    public function purchase(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $sessionId = $request->get('session_id');

        if (str_starts_with($sessionId, 'test_session_')) {
            //コンビニ支払い：即保存
            $productId = Product::where('is_purchased', 0)->first()->id ?? 1;
            $userId = Auth::id();

            Purchase::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'payment' => 0,
                'postal_code' => session('purchase_postal_code'),
                'address' => session('purchase_address'),
                'building' => session('purchase_building'),
            ]);

            Product::where('id', $productId)->update(['is_purchased' => 1]);
            return redirect('/');
        } else {
            // 🔹 カード支払い
            $session = Session::retrieve($sessionId);
            $productId = $session->metadata->product_id;
            $userId = $session->metadata->user_id;

            if (!Auth::check()) {
                Auth::loginUsingId($userId);
            }

            Purchase::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'payment' => 1,
                'postal_code' => session('purchase_postal_code'),
                'address' => session('purchase_address'),
                'building' => session('purchase_building'),
            ]);

            Product::where('id', $productId)->update(['is_purchased' => 1]);

            session()->forget([
                'purchase_postal_code',
                'purchase_address',
                'purchase_building',
            ]);

            return redirect('/');
        }
    }
}
