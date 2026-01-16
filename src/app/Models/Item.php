<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Like;
use App\Models\Comment;
use App\Models\Condition;


class Item extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'name',
        'brand_name',
        'description',
        'condition_id',
        'price',
        'image_path',
        'user_id',
    ];

    // ==================== リレーション ====================

    // ItemはUser(出品者)に属する (多対1の関係)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ItemはCondition(商品状態)に属する (多対1の関係)
    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    // ItemはCategoryに複数属する (多対多の関係)
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    // ItemはPurchaseに1対1で属する (売れたかどうかの情報)
    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    // Itemは複数のLikeを持つ (1対多の関係)
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // Itemは複数のCommentを持つ (1対多の関係)
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // ==================== 便利メソッド ====================

    /**
     * ログインユーザーがこの商品をいいねしているか判定
     * Bladeで $item->liked() として使用
     */
    public function liked(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        return Like::where(['item_id' => $this->id, 'user_id' => Auth::id()])->exists();
    }

    /**
     * この商品のいいね数を取得
     * Bladeで $item->likeCount() として使用
     */
    public function likeCount(): int
    {
        return $this->likes()->count();
    }

    /**
     * この商品が売却済みか判定
     * Bladeで $item->sold() として使用
     */
    public function sold(): bool
    {
        return $this->purchase()->exists();
    }

    /**
     * ログインユーザーの出品商品か判定
     * Bladeで $item->mine() として使用
     */
    public function mine(): bool
    {
        return $this->user_id === Auth::id();
    }

    /**
     * 商品名で検索するスコープ
     * コントローラーで Item::item('検索ワード')->get() として使用
     */
    public function scopeItem($query, $itemName)
    {
        return $query->where('name', 'like', '%' . $itemName . '%');
    }
}