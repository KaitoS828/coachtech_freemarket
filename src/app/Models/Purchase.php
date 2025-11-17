<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// 💡 他のリレーションに必要な use 文を追記 (例: use App\Models\Item;)

class Purchase extends Model
{
    use HasFactory;

    // ★★★ 以下のプロパティを追記します ★★★
    protected $fillable = [
        'user_id',
        'item_id',
        'payment_method',
        'shipping_post_code', // ★必須
        'shipping_address',   // ★必須
        'shipping_building',
    ];

    
}