<?php

namespace App\Http\Controllers;


use App\Models\Purchase;  // ← スペースを追加
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Http\Requests\MessageRequest;

class MessageController extends Controller
{
    public function show($purchaseId){

        // 購入情報の取得（プロフィール画像も一緒に取得）
        $purchase = Purchase::with([
            'item.user.profile',      // 商品の出品者とそのプロフィール
            'user.profile',            // 購入者とそのプロフィール
            'messages.user.profile'    // メッセージの送信者とそのプロフィール
        ])->findOrFail($purchaseId);

        //ログインユーザーの取得
        $user = Auth::user();

        //メッセージの取得
        $messages = $purchase->messages;

        // 相手から送信された未読メッセージを既読にする
        $purchase->messages()
            ->where('user_id', '!=', $user->id)  // 自分以外のメッセージ
            ->where('is_read', 0)                 // 未読
            ->update(['is_read' => 1]);           // 既読に更新

        // 商品の取得
        $item = $purchase->item;

        // 取引相手を取得
        if ($purchase->user_id === $user->id){
            // ログインユーザーが購入者の場合、相手は出品者
            $partner = $purchase->item->user;
        }else{
            // ログインユーザーが出品者の場合、相手は購入者
            $partner = $purchase->user;
        }


        // 他の取引中の商品を追加（進行中 + 購入者評価済み）
        $ongoingpurchase = Purchase::where(function($query) use ($user) {
            $query->where('user_id', $user->id)
            ->orWhereHas('item', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        })
        ->whereIn('status', [0, 1])
        ->where('id', '!=', $purchaseId)
        ->get();


        // ビューに渡す
        return view ('message.show',
        [
            'purchase' => $purchase,
            'messages' => $messages,
            'item' => $item,
            'user' => $user,
            'partner' => $partner,
            'ongoingpurchase' => $ongoingpurchase,
        ]);



    }

    public function store(MessageRequest $request, $purchaseId){
        // 購入者情報の取得
        $purchase = Purchase::findOrFail($purchaseId);//URLから受け取った値を使う

        // ユーザーの取得
        $user = Auth::user();

        // データを保存
        $message = new Message();
        $message->purchase_id = $purchase->id;
        $message->user_id = $user->id;
        $message->content = $request->input('content');

        // 画像保存処理
        //画像がアップロードされるか確認
        if($request->hasFile('image')){
            //public/messages/に保存
            $path = $request->file('image')->store('messages','public');
            // パスをデータベースに保存
            $message->image_path = $path;
        }
        $message->save();   

        // 保存後のリダイレクト
        return redirect()->route('message.show', ['purchaseId' => $purchase->id]);
        
    }

    // メッセージ編集機能
    public function edit($purchaseId, $messageId){
        // メッセージを取得
        $message = Message::findOrFail($messageId);

        // ユーザーを確認
        $user = Auth::user();
        
        // ビューに渡す
        return view('message.edit', [
            'message' => $message,
            'user' => $user,
        ]);
        
    }

    // メッセージ編集機能
    public function update(MessageRequest $request, $purchaseId, $messageId){
        
        // メッセージを取得
        $message = Message::findOrFail($messageId);

        // ユーザを確認
        $user = Auth::user();

        // メッセージ内容を更新
        $message->content = $request->input('content');
        $message->save();

        // 更新後のリダイレクト
        return redirect()->route('message.show', ['purchaseId' => $purchaseId]);

    }

    // メッセージ削除機能
    public function destroy($purchaseId,$messageId){

        // メッセージを取得
        $message = Message::findOrFail($messageId);

        // メッセージを削除
        $message->delete();

        // リダイレクト
        return redirect()->route('message.show', ['purchaseId' => $purchaseId]);
    }
}