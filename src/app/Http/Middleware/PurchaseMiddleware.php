<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

/**
 * 購入ミドルウェア
 * 
 * 出品者が自分の商品を購入できないようにガードする
 * mine()メソッドを使用して判定（Itemモデルで定義済み）
 */
class PurchaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // ルートパラメータから商品IDを取得
        $itemId = $request->route()->parameter('id');
        
        // 商品を取得
        $item = Item::find($itemId);
        
        // 商品が見つからない場合は404エラー
        if (!$item) {
            abort(404, '商品が見つかりません');
        }
        
        // 💡 自分の出品商品の場合はリダイレクトでガード
        // Itemモデルのmine()メソッドを使用
        if ($item->mine()) {
            return redirect()
                ->route('item.show', ['id' => $itemId])
                ->with('error', '出品者は自分の商品を購入できません');
        }

        return $next($request);
    }
}
