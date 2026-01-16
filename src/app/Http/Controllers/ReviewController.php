<?php

namespace App\Http\Controllers;


use App\Models\Purchase;    
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ReviewRequest;



use App\Events\TransactionCompletedEvent; // イベントのインポート

class ReviewController extends Controller
{
    public function store(ReviewRequest $request, $purchaseId)
    {
        // 取引情報を取得
        $purchase = Purchase::with(['item.user', 'user'])->findOrFail($purchaseId);

        // ログインユーザーを取得
        $user = Auth::user();

        // 評価される人を判定
        if ($purchase->user_id === $user->id) {
            // ログインユーザーが購入者 → 出品者を評価
            $receiverId = $purchase->item->user_id;
        } else {
            // ログインユーザーが出品者 → 購入者を評価
            $receiverId = $purchase->user_id;
        }

        // 評価を保存
        Review::create([
            'purchase_id' => $purchase->id,
            'user_id' => $user->id,
            'receiver_id' => $receiverId,
            'rate' => $request->rate,
        ]);

        // 取引ステータスを更新
        // 購入者の評価 → status = 1 (出品者の評価待ち)
        // 出品者の評価 → status = 2 (完了)
        if ($purchase->user_id === $user->id) {
            // 購入者が評価した場合
            $purchase->status = 1;
        } else {
            // 出品者が評価した場合
            $purchase->status = 2;
        }
        $purchase->save();

        // 取引完了(status=2)ならメール送信イベント発火
        if ($purchase->status === 2) {
            event(new TransactionCompletedEvent($purchase));
        }

        // 商品一覧画面にリダイレクト
        return redirect()->route('item.index');
    }
}
