<?php

use Illuminate\Support\Facades\Route; // ルーティングのために必須
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\Auth\LoginController; // 手動で定義したログインコントローラー
use App\Http\Controllers\Auth\RegisterController; // カスタムで定義した登録コントローラー
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController; // Fortifyのログイン処理
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReviewController; // 評価機能用

// トップページ
Route::get('/', [ItemController::class, 'index'])->name('item.index');

// 会員登録画面はfortify側でルーティングされているため、ここでは定義しない。

// ログイン画面
Route::get('/login', [LoginController::class, 'create'])
->middleware(['guest', 'email']) //未ログインかつ、未認証セッションチェック
->name('login'); //この処理はログインという名前で呼び出せる

//ログイン処理
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware(['guest',]);//未ログインの人のみ


// 認証機能のグループ化（この中のものは全て認証が必要）
Route::middleware(['auth','verified'])->group(function () {
    //↑応用編：email認証をしているかの認証も追加

        //プロフィール表示画面 (マイページTOP)
        Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage.show');

        // プロフィール編集・初回設定画面 (GET: フォーム表示, POST: 更新処理)
        // GET: 編集フォームの表示 (GETが定義されていることを確認)
        Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    
        // PATCH /mypage/profile
        Route::patch('/mypage/profile', [ProfileController::class, 'update'])
        ->name('mypage.profile.update');

        // GET: 出品フォームの表示
        Route::get('/sell', [ItemController::class, 'create'])->name('item.create');
        // POST: 出品データの保存処理
        Route::post('/sell', [ItemController::class, 'store'])->name('item.store');

        // 購入手続き画面へのルートを定義
        // purchaseミドルウェア: 出品者が自分の商品を購入できないようガード
        Route::get('/item/{id}/purchase/create', [PurchaseController::class, 'create'])
            ->middleware('purchase')
            ->name('item.purchase.create');

        // コメントはログインしたユーザーのみ投稿可能
        Route::post('/comment/store', [CommentController::class, 'store'])->name('comment.store');

        // いいねの追加・削除はログインしたユーザーのみ可能
        Route::post('/like/{itemId}/toggle', [LikeController::class, 'toggle'])->name('like.toggle');
        
        // [決済実行ルート] - POST //purchase/{id} 
        // purchaseミドルウェア: 出品者が自分の商品を購入できないようガード
        Route::post('/purchase/{id}', [PurchaseController::class, 'store'])
            ->middleware('purchase')
            ->name('purchase.store');

        // 購入画面（GET）
        // purchaseミドルウェア: 出品者が自分の商品を購入できないようガード
        Route::get('/purchase/{id}', [PurchaseController::class, 'create'])
            ->middleware('purchase')
            ->name('purchase.create');

        // [住所変更画面] - GET 
        Route::get('/purchase/address/{id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');

        //[住所更新処理] - PATCH /
        Route::patch('/purchase/address/{id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
        
        // [決済成功時のコールバック]
        Route::get('/purchase/success/{itemId}', [PurchaseController::class, 'success'])->name('checkout.success');

        Route::patch('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');


        //*******追加機能*******

        //メッセージ機能
        Route::get('/message/{purchaseId}', [MessageController::class, 'show'])->name('message.show');

        Route::post('/message/{purchaseId}', [MessageController::class,'store'])->name('message.store');

        ///メッセージ編集機能
        Route::get('/message/{purchaseId}/{messageId}/edit', [MessageController::class, 'edit'])->name('message.edit');

        // メッセージ更新機能
        Route::patch('/message/{purchaseId}/{messageId}', [MessageController::class,'update'])->name('message.update');

        // メッセージ削除機能
        Route::delete('/message/{purchaseId}/{messageId}', [MessageController::class,'destroy'])->name('message.destroy');

        // 評価機能
        Route::post('/purchase/{purchaseId}/review', [ReviewController::class, 'store'])->name('review.store');

});

    //商品詳細画面
    Route::get('/item/{id}', [ItemController::class, 'show'])->name('item.show');

            // 登録フォーム表示
        Route::get('/register', [RegisterController::class, 'create']) // Auth\を取り、useしたRegisterControllerを参照
            ->middleware('guest')
            ->name('register');

        // 登録処理 (RegisterRequestでバリデーション)
        Route::post('/register', [RegisterController::class, 'store']) 
            ->middleware('guest');



    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['auth', 'throttle:6,1'])
        ->name('verification.send');