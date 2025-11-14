<p>{{ $deal->sellingUser->profile->username }} 様</p>

<p>以下の商品の取引が完了しました：</p>

<p>商品名：{{ $deal->product->name }}</p>
<p>購入者：{{ $deal->purchasingUser->profile->username }}</p>

<p>取引画面にて詳細をご確認ください。</p>