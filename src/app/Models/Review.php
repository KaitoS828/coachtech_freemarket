<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'user_id',
        'receiver_id', 
        'rate',
        'comment',
    ];

    // 評価をする取引
    public function purchase(){
        return $this->belongsTo(Purchase::class);
    }

    // 評価する人
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    // 評価される人
    public function receiver(){
        return $this->belongsTo(User::class, 'receiver_id');
    }
}


