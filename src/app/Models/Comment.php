<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'comment',
    ];

    public function user()
{
    // コメントは一人のユーザーに属する（多対一）
    // 外部キーは user_id を使用
    return $this->belongsTo(User::class);
}

    public function item()
    {
        // コメントは一つのアイテムに属する（多対一）
        // 外部キーは item_id を使用
        return $this->belongsTo(Item::class);
    }

}
