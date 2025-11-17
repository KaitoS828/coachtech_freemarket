<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Like;
use App\Models\Item;
use Illuminate\Routing\Controller;

class LikeController extends Controller
{
    // いいねの追加または削除を行う
    public function toggle($id)
    {
        // 1. ログインユーザーのIDを取得
        $userId = Auth::id();
        
        // 2. 既にいいねが存在するか検索
        $like = Like::where('user_id', $userId)
                    ->where('item_id', $id)
                    ->first(); // 最初のレコードを取得

        // 3. 存在チェックと処理の切り替え
        if ($like) {
            // 既に存在する場合: 削除 (いいね解除)
            $like->delete();
        } else {
            // 存在しない場合: 新規作成 (いいね)
            Like::create([
                'user_id' => $userId,
                'item_id' => $id,
            ]);
        }

        // 4. 商品詳細ページへリダイレクト
        return redirect()->route('item.show', ['id' => $id]);
}
    
}
