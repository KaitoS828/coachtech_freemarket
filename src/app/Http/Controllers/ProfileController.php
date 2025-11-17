<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; //ログインユーザーの取得
use App\Models\Profile; //Profileモデルの使用
use Illuminate\Support\Facades\DB; //DBファサードの使用
use Illuminate\Support\Facades\Storage; 
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    //---プロフィール表示---
    public function edit(){ //editメソッドでプロフィール編集画面を表示
        $user = Auth::user(); // ログインユーザーの取得（誰がログインしているか）

        $profile = $user->profile; // ユーザーに関連付けられたプロフィールを取得

        //フォームに返す
        return view('profile.edit', compact('user', 'profile'));
    }

    //---プロフィール更新---
    public function update(ProfileRequest $request)
    {
        // 1. バリデーションの実行
        // 💡 ここではリクエストファイル（ProfileRequest.php）の代わりに、シンプルな validate() を使用
        $request->validate([
            'name' => 'required|string|max:20', // ユーザー名 (設計書)
            'post_code' => ['required', 'regex:/^\d{3}-\d{4}$/'], // 郵便番号 (ハイフンあり8文字)
            'address' => 'required|string|max:255', // 住所
            'building' => 'nullable|string|max:255', // 建物名 (任意)
            'image' => 'nullable|image|mimes:jpeg,png|max:2048', // プロフィール画像 (jpeg, png のみ)
        ]);
        
        $user = Auth::user();
        $profileData = $request->validated(); // ★ validated() を使って安全なデータのみを取得

        // 2. トランザクション処理 (usersとprofilesの同時更新)
        DB::transaction(function () use ($request, $user, $profileData) {
            
            // 2-1. 画像ファイルの処理 (FN029に準拠)
            if ($request->hasFile('image')) {
                // 古い画像があれば削除
                if ($user->profile && $user->profile->image_path) {
                    Storage::disk('public')->delete($user->profile->image_path);
                }
                // 新しいファイルを public/profiles ディレクトリに保存
                $path = $request->file('image')->store('public/profiles');
                $profileData['image_path'] = str_replace('public/', '', $path);
            }

            // 2-2. usersテーブルの更新 (ユーザー名)
            $user->name = $request->name;
            $user->save();

            // 2-3. profilesテーブルの更新/作成 (住所, 画像パス)
            // 💡 updateOrCreate: profileレコードが存在すれば更新、なければ新規作成
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        });

        // 3. プロフィール画面へリダイレクト
        return redirect()->route('mypage.show')->with('success', 'プロフィールを更新しました！');
    }


    //---マイページ表示---
        public function show(){
            /** @var \App\Models\User $user */
            $user = Auth::user(); 

            // 1. プロフィール情報を取得し、画像表示のために $profile 変数に格納
            $profile = $user->profile ?? new Profile(); // 

            // 出品した商品一覧を取得 (既存ロジック)
            $soldItems = $user->items()->get(); 
            // 購入した商品一覧を取得 (既存ロジック)
            $purchasedItems = $user->purchasedItems()->get(); 

            // フォームに返す
            return view('profile.show',[
                'user' => $user, // show.blade.phpに$userを渡す
                'profile' => $profile, // show.blade.phpに画像を渡す
                'soldItems' => $soldItems, // 出品商品一覧を渡す
                'purchasedItems' => $purchasedItems, // 購入商品一覧を渡す
            ]);
        }
}