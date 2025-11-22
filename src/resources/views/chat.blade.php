@extends('layouts.app')

@section('title')
取引チャット画面
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('css/chat.css')}}">
@endsection

@section('content')
<div class="chat-content">
    <aside class="chat-side-bar">
        <h1 class="chat-side-bar-title">
            その他の取引
        </h1>
        <div class="chat-side-bar-deals">
            @foreach($deals as $sideDeal)
            <div class="chat-side-bar-deal-item-wrapper">
                <a href="/item/{{$sideDeal->product_id}}/chat" class="chat-side-bar-deal-item">
                    {{$sideDeal->product->name}}
                </a>
            </div>
            @endforeach
        </div>
    </aside>
    <div class="chat-main-content">
        <form action="/deal/{{$deal->product->id}}/complete" class="chat-main-content-product-form" method="post">
            @csrf
            <input type="hidden" name="deal_id" value="{{$deal->id}}">
            <div class="chat-main-content-product-guide">
                <div class="chat-main-content-product-guide-user">
                    <div class="chat-main-content-product-guide-user-image-wrapper">
                        @if($user->id === $deal->selling_user_id)
                        <img src="{{asset('storage/images/' . $deal->purchasingUser->profile->image)}}" alt="{{$deal->purchasingUser->profile->username}}の画像" class="chat-main-content-product-guide-user-image">
                        @else
                        <img src="{{asset('storage/images/' . $deal->sellingUser->profile->image)}}" alt="{{$deal->sellingUser->profile->username}}の画像" class="chat-main-content-product-guide-user-image">
                        @endif
                    </div>
                    <p class="chat-main-content-product-guide-user-name">
                        @if($user->id === $deal->selling_user_id)
                        「{{$deal->purchasingUser->profile->username}}」さんとの取引画面
                        @else
                        「{{$deal->sellingUser->profile->username}}」さんとの取引画面
                        @endif
                    </p>
                </div>
                @if($user->id !== $deal->selling_user_id)
                <div class="chat-main-content-product-guide-button-wrapper">
                    <button class="chat-main-content-product-guide-button" type="submit">
                        取引を完了する
                    </button>
                </div>
                @endif
            </div>
            <div class="chat-main-content-product-information">
                <div class="chat-main-content-product-information-image-wrapper">
                    <img src="{{asset('storage/images/' . $deal->product->image)}}" alt="{{$deal->product->name}}の画像" class="chat-main-content-product-information-image">
                </div>
                <div class="chat-main-content-product-information-detail">
                    <p class="chat-main-content-product-information-detail-name">
                        {{$deal->product->name}}
                    </p>
                    <p class="chat-main-content-product-information-detail-price">
                        ¥ <span class="chat-main-content-product-information-detail-price-emphasis">{{number_format($deal->product->price)}}</span>(税込)
                    </p>
                </div>
            </div>
        </form>
        <div class="chat-main-content-conversations">
            @foreach($deal->chats as $chat)
            @if($chat->user_id === $user->id)
            <div class="chat-main-content-conversation-item is-right">
                <div class="chat-main-content-conversation-item-user">
                    <p class="chat-main-content-conversation-item-user-name">
                        {{$chat->user->profile->username}}
                    </p>
                    <div class="chat-main-content-conversation-item-user-image-wrapper">
                        <img src="{{asset('storage/images/' . $chat->user->profile->image)}}" alt="{{$chat->user->profile->username}}の画像" class="chat-main-content-conversation-item-user-image">
                    </div>
                </div>
                <div class="chat-main-content-conversation-item-message-wrapper">
                    @if(isset($editingChat) && $editingChat->id === $chat->id)
                    <form action="/item/{{$deal->product_id}}/chat/update" method="post">
                        @csrf
                        <input type="hidden" name="chat_id" value="{{ $editingChat->id }}">
                        <input type="text" name="message" value="{{ $editingChat->message }}" class="chat-main-content-conversations-post-form-input">
                        <button class="chat-main-content-conversation-item-button" type="submit">
                            更新
                        </button>
                    </form>
                    @else
                    <p class="chat-main-content-conversation-item-message">
                        {{$chat->message}}
                    </p>
                    @endif
                </div>
                @if(!empty($chat->image))
                <div class="chat-main-content-conversation-item-image-wrapper">
                    <img src="{{asset('storage/images/' . $chat->image)}}" alt="投稿された画像" class="chat-main-content-conversation-item-image">
                </div>
                @endif
                <div class="chat-main-content-conversation-item-buttons">
                    <form action="/item/{{$deal->product_id}}/chat/edit" class="chat-main-content-conversation-item-form-update" method="post">
                        @csrf
                        <input type="hidden" name="chat_id" value="{{$chat->id}}">
                        <button class="chat-main-content-conversation-item-button" type="submit">
                            編集
                        </button>
                    </form>
                    <form action="/item/{{$deal->product_id}}/chat/delete" class="chat-main-content-conversation-item-form-delete" method="post">
                        @csrf
                        <input type="hidden" name="chat_id" value="{{$chat->id}}">
                        <button class="chat-main-content-conversation-item-button">
                            削除
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="chat-main-content-conversation-item is-left">
                <div class="chat-main-content-conversation-item-user-left">
                    <div class="chat-main-content-conversation-item-user-image-wrapper">
                        <img src="{{asset('storage/images/' . $chat->user->profile->image)}}" alt="{{$chat->user->profile->username}}の画像" class="chat-main-content-conversation-item-user-image">
                    </div>
                    <p class="chat-main-content-conversation-item-user-name-left">
                        {{$chat->user->profile->username}}
                    </p>
                </div>
                <p class="chat-main-content-conversation-item-message">
                    {{$chat->message}}
                </p>
                @if(!empty($chat->image))
                <div class="chat-main-content-conversation-item-image-wrapper-left">
                    <img src="{{asset('storage/images/' . $chat->image)}}" alt="投稿された画像" class="chat-main-content-conversation-item-image">
                </div>
                @endif
            </div>
            @endif
            @endforeach
            @error('message')
            <p class="error">
                {{$message}}
            </p>
            @enderror
            @error('image')
            <p class="error">
                {{$message}}
            </p>
            @enderror
            <div class="chat-main-content-conversations-post">
                <form action="/item/{{$deal->product_id}}/chat" class="chat-main-content-conversations-post-form" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="deal_id" value="{{$deal->id}}">
                    <input type="text" class="chat-main-content-conversations-post-form-input" name="message" value="{{old('message', $messageDraft)}}" placeholder="取引メッセージを記入してください">
                    <div class="chat-main-content-conversations-post-form-image-upload">
                        <label for="file-upload" class="chat-main-content-conversations-post-form-image-upload-label">
                            画像を追加
                        </label>
                        <input type="file" class="chat-main-content-conversations-post-form-image-upload-input" id="file-upload" name="image">
                    </div>
                    <div class="chat-main-content-conversations-post-form-button-wrapper">
                        <button class="chat-main-content-conversations-post-form-button" type="submit">
                            <img src="{{asset('images/post.jpg')}}" alt="" class="chat-main-content-conversatinons-post-form-button-image">
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($is_deal->is_deal && !$hasEvaluated)
<div class="modal" id="evaluation-modal">
    <div class="modal-content">
        <h1 class="modal-content-title">
            取引が完了しました。
        </h1>
        <form action="/item/{{$deal->product->id}}/chat/evaluation" class="modal-content-form" method="post">
            @csrf
            <input type="hidden" name="deal_id" value="{{$deal->id}}">
            @if($user->id === $deal->selling_user_id)
            <input type="hidden" name="other_user_id" value="{{$deal->purchasing_user_id}}">
            @else
            <input type="hidden" name="other_user_id" value="{{$deal->selling_user_id}}">
            @endif
            <p class="modal-content-form-sentense">
                今回の取引相手はどうでしたか？
            </p>
            <div class="modal-content-form-stars">
                @for($i=1; $i<=5; $i++)
                    <div class="star" data-value="{{$i}}">★</div>
            @endfor
            <input type="hidden" name="rating" id="rating-value">
    </div>
    <div class="modal-content-form-button-wrapper">
        <button class="modal-content-form-button" type="submit">
            送信する
        </button>
    </div>
    </form>
</div>
</div>
@endif

@endsection

@section('js')
<script>
    const chatInput = document.querySelector('.chat-main-content-conversations-post-form-input');
    const itemId = "{{ $deal->product_id }}";

    chatInput.addEventListener('input', () => {
        fetch(`/item/${itemId}/chat/save-draft`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                message: chatInput.value
            })
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const stars = document.querySelectorAll('.modal-content-form-stars .star');
        const ratingInput = document.getElementById('rating-value');

        stars.forEach((star, index) => {
            star.addEventListener('click', () => {
                ratingInput.value = star.dataset.value;
                stars.forEach((s, i) => {
                    if (i < index + 1) {
                        s.classList.add('selected');
                    } else {
                        s.classList.remove('selected');
                    }
                });
            });

            // ホバー時の色変化
            star.addEventListener('mouseover', () => {
                stars.forEach((s, i) => {
                    if (i <= index) s.style.color = 'gold';
                    else s.style.color = '#ccc';
                });
            });

            star.addEventListener('mouseout', () => {
                stars.forEach((s, i) => {
                    if (s.classList.contains('selected')) s.style.color = 'gold';
                    else s.style.color = '#ccc';
                });
            });
        });
    });
</script>
@endsection