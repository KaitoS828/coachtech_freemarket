<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;

    // 商品状態の定数定義
    public static $UNUSED = 1;          // 良好
    public static $HARMLESS = 2;        // 目立った傷や汚れなし
    public static $HARMED = 3;          // やや傷や汚れあり
    public static $BAD_CONDITION = 4;   // 状態が悪い
    
    protected $fillable = [
        'condition', 
    ];
}
