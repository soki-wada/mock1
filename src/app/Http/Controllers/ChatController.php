<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deal;
use App\Models\Product;
use App\Models\Chat;
use App\Http\Requests\ChatRequest;
use App\Models\Evaluation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Mail\DealCompletedMail;
use Illuminate\Support\Facades\Mail;


class ChatController extends Controller
{
    //
    public function postDeal(Request $request, $item_id){
        $product = Product::find($item_id);
        $user = Auth::user();
        Deal::create([
            'selling_user_id' => $product->user_id,
            'purchasing_user_id' => $user->id,
            'product_id' => $item_id
        ]);
        return redirect("/item/$item_id/chat");
    }

    public function showChat($item_id){
        $user = Auth::user();
        $chats = Chat::where('product_id', $item_id)->where('user_id', '!=', $user->id)->where('is_read', false)->get();
        foreach($chats as $chat){
            $chat->update([
                'is_read' => true
            ]);
        }

        $deals = Deal::where(function ($query) use ($user) {
            $query->where('selling_user_id', $user->id)
                ->orWhere('purchasing_user_id', $user->id);
        })
            ->where('product_id', '!=', $item_id)
            ->where('is_deal', false)
            ->with(['product', 'chats' => function ($q) {
                $q->latest();
            }])
            ->get()
            ->sortByDesc(function ($deal) {
                $latestChat = $deal->chats->first();
                return $latestChat ? $latestChat->created_at : now()->subYears(100);
            });        $deal = Deal::where('product_id', $item_id)->first();
        $messageDraft = session("chat_draft_{$item_id}", '');

        $is_deal = Deal::where('product_id', $item_id)->with('evaluations')->first();
        $hasEvaluated = $is_deal->evaluations()->where('user_id', $user->id)->first();

        return view('chat', compact('deals', 'user','deal', 'messageDraft', 'is_deal', 'hasEvaluated'));
    }

    public function saveDraft(Request $request, $item_id)
    {
        $request->session()->put("chat_draft_{$item_id}", $request->input('message'));
        return response()->json(['status' => 'saved']);
    }

    public function postChat(ChatRequest $request, $item_id){
        $user = Auth::user();
        $chat = [
            'user_id' => $user->id,
            'deal_id' => $request->deal_id,
            'product_id' => $item_id,
            'message' => $request->message,
            'is_read' => false
        ];

        if(!empty($request->image)){
            $fileName = $request->file('image')->getClientOriginalName();
            $uniqueName = Str::uuid() . '_' . $fileName;
            $request->file('image')->storeAs('public/images', $uniqueName);
            $chat['image'] = basename($uniqueName);
        }

        Chat::create($chat);

        return redirect("item/$item_id/chat");
    }

    public function updateChat(Request $request, $item_id){


        return redirect("item/$item_id/chat");
    }

    public function deleteChat(Request $request, $item_id){
        $chat = Chat::find($request->chat_id);

        if ($chat && $chat->user_id === Auth::id()) {
            $chat->delete();
        }
        return redirect("item/$item_id/chat");
    }

    public function completeDeal(Request $request, $item_id){
        $deal = Deal::find($request->deal_id);
        $deal->update([
            'is_deal' => true
        ]);

        $seller = $deal->sellingUser;

        Mail::to($seller->email)->send(new DealCompletedMail($deal));

        return redirect("/item/$item_id/chat");
    }

    public function evaluation(Request $request, $item_id){
        $user = Auth::user();

        Evaluation::create([
            'user_id' => $user->id,
            'target_user_id' => $request->other_user_id,
            'deal_id' => $request->deal_id,
            'rating' => $request->rating,
        ]);

        return redirect('/');
    }
}
