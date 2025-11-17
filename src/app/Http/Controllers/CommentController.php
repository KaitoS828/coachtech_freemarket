<?php

namespace App\Http\Controllers; // ★namespaceが正しいか確認

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment; 
use App\Http\Requests\CommentRequest; 
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    public function store(CommentRequest $request)
    {
        // 1. バリデーションの実行（設計書に基づき required|max:255）
        $request->validate([
            'comment' => 'required|string|max:255', 
            'item_id' => 'required|exists:items,id', 
        ]);

        // 2. データの保存
        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $request->item_id, 
            'comment' => $request->comment, 
        ]);

        // 3. 商品詳細ページへリダイレクト
        return redirect()->route('item.show', ['id' => $request->item_id])
                        ->with('success', 'コメントを投稿しました！');
    }
}