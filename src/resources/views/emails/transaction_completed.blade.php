<!DOCTYPE html>
<html>
<head>
    <title>取引完了のお知らせ</title>
</head>
<body>
    <h1>取引完了のお知らせ</h1>
    <p>{{ $purchase->item->user->name }}様</p>
    <p>以下の商品の取引が完了しました。</p>
    
    <p>商品名: {{ $purchase->item->name }}</p>
    <p>商品価格: {{ $purchase->item->price }}円</p>
    
    <p>ご利用ありがとうございました。</p>
</body>
</html>