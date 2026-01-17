<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// 他のリレーションに必要な use 文を追記 (例: use App\Models\Item;)

class Purchase extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'item_id',
        'payment_method',
        'shipping_post_code', // 必須
        'shipping_address',   // 必須
        'shipping_building',
    ];

    // リレーション

    public function user(){

        // Userは複数のPurchaseを持つ (1対多の関係)
        return $this->belongsTo(User::class);
    }
    public function item(){

        // 購入は、一つの商品になる
        return $this->belongsTo(Item::class);
    }

    public function messages(){

        // Purchaseは複数のMessageを持つ (1対多の関係)
        return $this->hasMany(Message::class);
    }

}