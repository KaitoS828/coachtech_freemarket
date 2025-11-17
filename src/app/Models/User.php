<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Illuminate\Contracts\Auth\MustVerifyEmail; //e-mail認証機能を追加


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    // Userは複数のItem(出品した商品)を持つ (1対多の関係)
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // Userは1つのProfileを持つ (1対1の関係)
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    // 💡その他、likesやcommentsなど、ER図に登場するリレーションも必要に応じて追加します。
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    //
    // public function likedItems()
    // {
    //     return $this->belongsToMany(Item::class, 'likes', 'user_id', 'item_id')->withTimestamps();
    // }

    // Userは複数のCommentを持つ (1対多の関係)
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }



    /**
     * ユーザーが出品した商品のうち、売却済みのものを取得する
     */
    public function soldItems()
    {
        return $this->hasMany(Item::class, 'user_id')->whereHas('purchase');
    }

    /**
     * ユーザーが購入した商品を取得する
     * purchasesテーブルを中間テーブルとして利用
     */
    public function purchasedItems()
    {
        return $this->belongsToMany(Item::class, 'purchases', 'user_id', 'item_id')->withTimestamps();
    }
}