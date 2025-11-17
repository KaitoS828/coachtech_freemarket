<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // storeメソッドで使用
use App\Models\Item; 
use App\Models\User;
use App\Models\Profile;
use App\Models\Purchase; // storeメソッドで使用
use App\Http\Requests\PurchaseRequest; // storeメソッドで使用


class PurchaseController extends Controller
{
    /**
     * 商品購入画面（支払い方法・配送先選択）を表示する。
     * @param  int  $id
     */
    public function create($id)
    {
        // 1. 購入する商品情報を取得
        $item = Item::findOrFail($id);

        // 2. ログインユーザーと、そのプロフィール/住所情報を取得
        $user = Auth::user();
        
        // Profileモデルが存在しない場合に備え、新しいインスタンスを作成
        $profile = $user->profile ?? new Profile(); 

        // 3. 支払い方法のオプションを定義
        $paymentMethods = [
            'card' => 'クレジットカード',
            'convenience' => 'コンビニ支払い',
        ];
        
        // 4. Viewにデータを渡して購入画面を表示
        return view('purchase.create', compact('item', 'profile', 'paymentMethods'));
    }


    public function editAddress($id)
    {
        // 1. 商品情報と現在のプロフィール情報を取得
        $item = Item::findOrFail($id);
        $profile = Auth::user()->profile ?? new Profile();

        // 2. Viewにデータを渡す
        return view('purchase.address_edit', compact('item', 'profile'));
    }

    /**
     * 配送先住所を更新する (FN024-2)
     * 💡 updateAddress ルートに対応
     */
    public function updateAddress(Request $request, $id)
    {
        // 1. バリデーションの実行
        // 設計書の「郵便番号: ハイフンありの8文字」に準拠
        $request->validate([
            'post_code' => ['required', 'regex:/^\d{3}-\d{4}$/'], // 例: 123-4567
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ]);
        
        $user = Auth::user();
        
        // 2. Profile情報を更新または新規作成
        // 🚨 ここで Profile テーブルに住所情報を保存し、購入画面に反映させます。
        $user->$profile()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only(['post_code', 'address', 'building'])
        );
        
        // 3. 購入画面に戻る (FN024-2)
        return redirect()->route('purchase.create', ['itemId' => $id])->with('success', '配送先住所を更新しました。');
    }

    /**
     * 購入処理と決済を実行する (FN022/FN023)
     */
    public function store(PurchaseRequest $request, $id)
    {
        // 1. バリデーション（支払い方法の選択は必須）
        $request->validate([
            'payment_method' => 'required', 
        ]);
        
        // 2. ★★★ 住所情報取得と未設定チェック ★★★
        $profile = Auth::user()->profile; // ログインユーザーの最新のプロフィール情報を取得

        if (!$profile || !$profile->post_code || !$profile->address) {
            // 住所情報が未設定の場合は、エラーとして購入画面に戻す
            return redirect()->route('purchase.create', ['itemId' => $id])->with('error', '配送先住所が未設定です。');
        }

        // 3. トランザクション処理 (データの整合性を保証)
        DB::transaction(function () use ($request, $id, $profile) {
            
            $item = Item::findOrFail($id);

            // 🚨 既に売却済みでないかのチェック
            if ($item->is_sold) {
                // 購入フローでは二重購入を防ぐため、エラーを投げる
                throw new \Exception('この商品はすでに購入済みです。');
            }

            // 4. Purchaseレコードの作成 (FN022-1, 3)
            Purchase::create([
                'user_id' => Auth::id(),
                'item_id' => $id,
                'payment_method' => $request->payment_method,
                
                    // ★★★ 配送先情報を追加 ★★★
                    'shipping_post_code' => $profile->post_code,
                'shipping_address' => $profile->address,
                'shipping_building' => $profile->building,
            ]);

            // 5. Itemの状態を「sold」にする（FN022-2: SOLD表示の基盤）
            $item->is_sold = true; 
            $item->save();
            
            // 💡 実際にはここで Stripe などの決済API コールが入ります
        });

        // 6. 商品一覧画面へ遷移（FN022-4）
        return redirect()->route('item.index')->with('success', 'ご購入が完了しました！');
    }
}