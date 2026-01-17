<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class ItemController extends Controller
{
        public function index(Request $request)
    {
        $categories = Category::all();
        $keyword = $request->input('keyword'); 

        // 1. 基本クエリを作成 
        $query = Item::with(['categories', 'likes', 'comments']);

        // 2. 検索/タブの条件分岐でクエリを絞り込む
        if ($request->tab === 'mylist' && Auth::check()) {
            $likedItemIds = Auth::user()->likes()->pluck('item_id'); 
            
            // $query に whereIn を適用 (get() はまだ実行しない)
            $query->whereIn('id', $likedItemIds); 
        } 
        
        // Before: $query->where('name', 'LIKE', '%' . $keyword . '%');
        // After: $query->item($keyword); ← スコープメソッドでシンプルに
        if ($keyword) {
            $query->item($keyword);
        }
        
        // 4. 最終的な商品データを取得 (ここで初めて get() を実行)
        $items = $query->get();

        return view('item.index', compact('items', 'categories', 'keyword')); 
}

    // ---商品出品機能---
    public function create()
    {
        // 1. カテゴリ一覧を取得 
        $categories = Category::all();
        
        // 2. 商品の状態をconditionsテーブルから取得
        $conditions = Condition::pluck('condition', 'id');

        // 3. compact() で $conditions をビューに渡す
        return view('item.create', compact('categories', 'conditions'));
    }
    
    public function store(ExhibitionRequest $request) 
    {
        // ExhibitionRequestでバリデーションは既に完了済み
        
        // 処理の安全性を高めるため、トランザクションを使用
        DB::transaction(function () use ($request) {
            
            $itemData = $request->only(['name', 'description', 'brand_name', 'price', 'condition_id']);
            
            // 1. 商品画像アップロード 
            // Laravelのstorageディレクトリに保存し、パスを取得
            $path = $request->file('image')->store('public/items');
            
            // DBには public/ を除いた相対パスを保存
            $itemData['image_path'] = str_replace('public/', '', $path); 
            $itemData['user_id'] = Auth::id(); // ログインユーザーを出品者として設定
            
            // 2. itemsテーブルに商品を保存 
            $item = Item::create($itemData);
            
            // 3. カテゴリの紐づけ (多対多リレーション)
            // categories[] は配列で送られてくる。attach() で中間テーブルに挿入
            $item->categories()->attach($request->categories);
        });

        // 4. 出品後、トップページへリダイレクト
        return redirect()->route('item.index')->with('success', '商品を出品しました！');
    }

    /**
     * 商品詳細画面を表示する
     */
    public function show($id) 
    {
        // 購入記録（purchase）を with() で必ず取得する
        $item = Item::with(['categories', 'likes', 'comments.user', 'purchase'])->findOrFail($id); 
        
        return view('item.show', compact('item'));
    }

}