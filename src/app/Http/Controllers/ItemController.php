<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\User; // use App\Models\User; に修正
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    /**
     * 商品一覧（トップページ）を表示するためのメソッド
     * @param  \Illuminate\Http\Request  $request
     */
    public function index(Request $request)
{
    $categories = Category::all();
    $keyword = $request->input('keyword'); // ★検索キーワードを取得

    // 1. 基本クエリを作成 (N+1問題対策)
    $query = Item::with(['categories', 'likes', 'comments']);

    // 2. ★★★ 検索ロジックの適用 (FN016: 商品名で部分一致検索) ★★★
    if ($keyword) {
        // WHERE句を追加: 商品名 (name) で部分一致検索
        // 💡 データベースに送る前にクエリを絞り込みます
        $query->where('name', 'LIKE', '%' . $keyword . '%');
    }

    // 3. タブ（マイリスト）の条件分岐 (省略)

    // 4. クエリを実行して商品データを取得
    $items = $query->get(); // 絞り込まれたクエリを実行

    // 5. Viewにデータを渡す
    return view('item.index', compact('items', 'categories', 'keyword')); 
}

    // ---商品出品機能---
    public function create()
    {
        // 1. カテゴリ一覧を取得 (★これはOK)
        $categories = \App\Models\Category::all();
        
        // 2. ★商品の状態（$conditions）を定義
        $conditions = [
            '良好' => '良好',
            '目立った傷や汚れなし' => '目立った傷や汚れなし',
            'やや傷や汚れあり' => 'やや傷や汚れあり',
            '状態が悪い' => '状態が悪い',
        ];

        // 3. ★compact() で $conditions をビューに渡す
        return view('item.create', compact('categories', 'conditions'));
    }
    
    public function store(ExhibitionRequest $request) 
    {
        // ExhibitionRequestでバリデーションは既に完了済み
        
        // 処理の安全性を高めるため、トランザクションを使用
        DB::transaction(function () use ($request) {
            
            $itemData = $request->only(['name', 'description', 'brand_name', 'price', 'condition']);
            
            // 1. 商品画像アップロード (FN029)
            // Laravelのstorageディレクトリに保存し、パスを取得
            $path = $request->file('image')->store('public/items');
            
            // DBには public/ を除いた相対パスを保存
            $itemData['image_path'] = str_replace('public/', '', $path); 
            $itemData['user_id'] = Auth::id(); // ログインユーザーを出品者として設定
            
            // 2. itemsテーブルに商品を保存 (FN028)
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
        // ★購入記録（purchase）を with() で必ず取得する
        $item = Item::with(['categories', 'likes', 'comments.user', 'purchase'])->findOrFail($id); 
        
        return view('item.show', compact('item'));
    }

}