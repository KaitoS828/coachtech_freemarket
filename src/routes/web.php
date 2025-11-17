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
// トップページ
Route::get('/', [ItemController::class, 'index'])->name('item.index');

// 会員登録画面はfortify側でルーティングされているため、ここでは定義しない。

// ログイン画面
Route::get('/login', [LoginController::class, 'create'])
->middleware('guest') //未ログインの人のみ
->name('login'); //この処理はログインという名前で呼び出せる

//ログイン処理
// Fortifyが自動で用意するコントローラーを直接呼び出す
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
        // URI: /item/{item}/purchase/create
        Route::get('/item/{id}/purchase/create', [PurchaseController::class, 'create'])->name('item.purchase.create');

        // コメントはログインしたユーザーのみ投稿可能
        Route::post('/comment/store', [CommentController::class, 'store'])->name('comment.store');

        // いいねの追加・削除はログインしたユーザーのみ可能
        Route::post('/like/{itemId}/toggle', [LikeController::class, 'toggle'])->name('like.toggle');
        
        // [決済実行ルート] - POST //purchase/{id} (後で実装)
        Route::post('//purchase/{id}', [PurchaseController::class, 'store'])->name('purchase.store'); 

        // [住所変更画面] - GET /purchase/address/{itemId}
        Route::get('/purchase/address/{itemId}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');


        Route::patch('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

});

    //商品詳細画面
    Route::get('/item/{id}', [ItemController::class, 'show'])->name('item.show');

            // 登録フォーム表示
        Route::get('/register', [RegisterController::class, 'create']) // ★Auth\を取り、useしたRegisterControllerを参照
            ->middleware('guest')
            ->name('register');

        // 登録処理 (RegisterRequestでバリデーション)
        Route::post('/register', [RegisterController::class, 'store']) 
            ->middleware('guest');



    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['auth', 'throttle:6,1'])
        ->name('verification.send');