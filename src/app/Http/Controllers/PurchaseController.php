<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // storeメソッドで使用
use App\Models\Item; 

use App\Models\Profile;
use App\Models\Purchase; // storeメソッドで使用
use App\Http\Requests\PurchaseRequest; // storeメソッドで使用


class PurchaseController extends Controller
{
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
     * updateAddress ルートに対応
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
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only(['post_code', 'address', 'building'])
        );
        
        // 3. 購入画面に戻る (FN024-2)
        return redirect()->route('purchase.create', ['id' => $id])->with('success', '配送先住所を更新しました。');
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
        
        // 2. 住所情報取得と未設定チェック 
        $profile = Auth::user()->profile; // ログインユーザーの最新のプロフィール情報を取得

        if (!$profile || !$profile->post_code || !$profile->address) {
            // 住所情報が未設定の場合は、エラーとして購入画面に戻す
            return redirect()->route('purchase.create', ['id' => $id])->with('error', '配送先住所が未設定です。');
        }

        $item = Item::findOrFail($id);

        // 既に売却済みでないかのチェック
        if ($item->is_sold) {
            // 購入フローでは二重購入を防ぐため、エラーを投げる
            throw new \Exception('この商品はすでに購入済みです。');
        }

        // 3. コンビニ払いの場合は従来のCreate処理（簡易実装として）
        if ($request->payment_method === 'convenience') {
            DB::transaction(function () use ($request, $id, $profile, $item) {
                Purchase::create([
                    'user_id' => Auth::id(),
                    'item_id' => $id,
                    'payment_method' => $request->payment_method,
                    'shipping_post_code' => $profile->post_code,
                    'shipping_address' => $profile->address,
                    'shipping_building' => $profile->building,
                ]);
                $item->is_sold = true;
                $item->save();
            });
            return redirect()->route('item.index')->with('success', 'ご購入が完了しました！(コンビニ払い)');
        }

        // 4. クレジットカード決済 (Stripe Checkout)
        if ($request->payment_method === 'card') {
            // Stripeクライアントの初期化
            $stripe = new \Stripe\StripeClient(config('stripe.secret_key'));

            // Checkout Sessionの作成
            $checkout_session = $stripe->checkout->sessions->create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $item->name,
                        ],
                        'unit_amount' => $item->price,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                // 成功時のリダイレクト先 (セッションIDを含めない場合は単純に商品IDを渡す)
                'success_url' => route('checkout.success', ['itemId' => $item->id]),
                // キャンセル時のリダイレクト先
                'cancel_url' => route('purchase.create', ['id' => $item->id]),
            ]);

            // 決済ページへリダイレクト
            return redirect($checkout_session->url);
        }
    }

    /**
     * 決済成功時のコールバック (FN022/FN023)
     */
    public function success($itemId)
    {
        $item = Item::findOrFail($itemId);
        $user = Auth::user();
        $profile = $user->profile;

        // すでに売却済みの場合はトップへ（リロード対策）
        if ($item->is_sold) {
            return redirect()->route('item.index');
        }

        // DBトランザクションで注文記録
        DB::transaction(function () use ($item, $user, $profile) {
            Purchase::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'payment_method' => 'card', // クレジットカード固定
                'shipping_post_code' => $profile->post_code,
                'shipping_address' => $profile->address,
                'shipping_building' => $profile->building,
            ]);

            // Itemの状態を「sold」にする
            $item->is_sold = true;
            $item->save();

        });

        return redirect()->route('item.index')->with('success', 'ご購入が完了しました！');
    }
}