<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Like;
use App\Models\Comment;


class Item extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'name',
        'brand_name',
        'description',
        'condition',
        'price',
        'image_path',
        'user_id', // ログインユーザーIDも必須
    ];

    // ItemはUser(出品者)に属する (多対1の関係)
    public function user()
    {
        return $this->belongsTo(User::class);
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

    // Itemは複数のLikeやCommentを持つ (1対多の関係)
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // Itemは複数のCommentを持つ (1対多の関係)
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}